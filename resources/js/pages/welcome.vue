<template>
  <div>
    <div class="kanban">
      <div class="header">
        <h2 class="header__title">
          Kanban Board
        </h2>
        <div class="header__action">
          <button
            class="
              header__button button button--primary
            "
            @click="addColumn"
          >
            Add Column
          </button>
        </div>
      </div>
      <div class="board">
        <div class="board__item" v-for="(col, i) in columns" :key="i">
          <div class="col">
            <div class="col__header">
            <h3 class="col__title">
              {{ col.title }}
            </h3>
            <div class="col__actions">
              <div
                class="col__action col__action--edit"
                @click="editColumn(col)"
              ></div>
              <div
                class="col__action col__action--delete"
                @click="deleteColumn(col)"
              ></div>
            </div>
          </div>
          <draggable
            class="col__cards"
            :list="col.cards"
            group="cards"
            @start="drag = true"
            @end="drag = false"
            handle=".col__card__handle"
            @change="storeOrder"
          >
            <div
              class="col__card col__card--edit"
              v-for="(card, i) in col.cards"
              :key="i"
              @click="editItem(card)"
            >
              <div class="col__card__handle"></div>
              <h4 class="col__card__title">
                {{ card.title }}
              </h4>
            </div>
          </draggable>
          <button class="col__card__button button button--primary" @click="addItem(col.id)">
            Add Card
          </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Add/Edit Modal -->
    <modal name="item-form" height="auto">
      <form class="modal" @submit.prevent="saveItem">
        <h3 class="modal__title">{{ item.id ? "Edit" : "Add" }} Card</h3>
        <div class="modal__group">
          <label for="title" class="modal__label">Title</label>
          <input
            type="text"
            class="modal__input"
            v-model="item.title"
            id="title"
            name="title"
            placeholder="Title"
          />
        </div>
        <div class="modal__group">
          <label for="description" class="modal__label">Description</label>
          <textarea
            name="description"
            class="modal__input"
            v-model="item.description"
            id="description"
            cols="30"
            rows="10"
            placeholder="Description"
          ></textarea>
        </div>

        <div class="modal__footer">
          <div class="modal__actions">
            <button
              class="button button--secondary modal__cancel"
              @click="cancel"
              type="button"
            >
              Cancel
            </button>
            <button class="button button--primary modal__submit" type="submit">
              Submit
            </button>
          </div>
        </div>
      </form>
    </modal>

    <!-- Column Add/Edit Modal -->
    <modal name="column-form" height="auto">
      <form  class="modal" @submit.prevent="saveColumn">
        <h3 class="modal__title">{{ column.id ? "Edit" : "Add" }} Column</h3>
        <div class="modal__group">
          <label for="colum_title" class="modal__label">Title</label>
          <input
            type="text"
            class="modal__input"
            v-model="column.title"
            id="colum_title"
            name="title"
            placeholder="Title"
          />
        </div>

        <div class="modal__footer">
          <div class="modal__actions">
            <button class="modal__cancel button button--secondary" @click="cancelColumn" type="button">
              Cancel
            </button>
            <button class="modal__submit button button--primary" type="submit">Submit</button>
          </div>
        </div>
      </form>
    </modal>

    <!-- Column Delete Modal -->
    <modal  name="column-delete" height="auto">
      <form class="modal" @submit.prevent="submitDeleteColumn">
        <h3 class="modal__title">
          Are you sure you want to delete this column
        </h3>

        <div class="modal__footer">
          <div class="modal__actions">
            <button class="modal__cancel button button--secondary" @click="cancelColumnDelete" type="button">
              Cancel
            </button>
            <button class="modal__submit button button--primary" type="submit">Delete</button>
          </div>
        </div>
      </form>
    </modal>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import axios from "axios";
export default {
  components: {
    draggable,
  },
  data: () => {
    return {
      columns: [],
      item: {
        id: null,
        title: "",
        description: "",
        column_id: null,
      },
      column: {
        id: null,
        title: "",
      },
      orderTimeout: 0,
    };
  },

  mounted() {
    this.getColumns();
  },
  methods: {
    getColumns() {
      axios.get("/api/columns").then(({ data }) => {
        this.columns = data;
      });
    },
    addColumn() {
      let id, title;
      this.column = { id, title, cards: [] };
      this.$modal.show("column-form");
    },
    editColumn({ id, title }) {
      this.column = { id, title };
      this.$modal.show("column-form");
    },
    saveColumn() {
      axios.post("api/columns", this.column).then((response) => {
        this.getColumns();
        this.$modal.hide("column-form");
      });
    },
    cancelColumn() {
      this.$modal.hide("column-form");
    },
    deleteColumn({ id, title }) {
      this.column = { id, title };
      this.$modal.show("column-delete");
    },
    cancelColumnDelete(){
      this.$modal.hide("column-delete");
    },
    submitDeleteColumn() {
      axios.delete(`api/columns/${this.column.id}`).then((response) => {
        this.getColumns();
        this.$modal.hide("column-delete");
      });
    },
    editItem({ id, title, description, column_id }) {
      this.item = { id, title, description, column_id };
      this.$modal.show("item-form");
    },
    addItem(column_id) {
      let id, title, description;
      this.item = { id, title, description, column_id };
      this.$modal.show("item-form");
    },
    cancel() {
      this.$modal.hide("item-form");
    },
    saveItem() {
      let index = this.columns.findIndex((c) => c.id == this.item.column_id);
      axios.post("api/cards", this.item).then((response) => {
        this.getColumns();
        this.$modal.hide("item-form");
      });
    },
    storeOrder(event) {
      console.log(event);
      if (!!event.removed || !!event.moved) {
        let order = [];
        this.columns.forEach((col) => {
          order.push({
            column_id: col.id,
            cards: col.cards.map((c) => c.id),
          });
        });
        axios.post("/api/cards/reorder", { order }).then((response) => {
          this.getColumns();
        });
      }
    },
  },
};
</script>
