<template>
  <div class="kanban">
    <div class="kanban__header">
      <div class="kanban__header__title">
        <h2>Kanban Board</h2>
      </div>
      <div class="kanban__header__action">
        <button
          class="kanban__header__add-column kanban__header__add_column--active"
          @click="addColumn"
        >
          Add Column
        </button>
      </div>
    </div>
    <div class="kanban__board">
      <div class="kanban__board__column" v-for="(col, i) in columns" :key="i">
        <div class="kanban__board__column__header">
          <div class="kanban__board__column__title">
            <h3>{{ col.title }}</h3>
          </div>
          <div class="kanban__board__column__actions">
            <div class="kanban__board__column__edit" @click="editColumn(col)"></div>
            <div class="kanban__board__column__delete" @click="deleteColumn(col)"></div>
          </div>
        </div>
        <draggable
          class="kanban__board__column__cards"
          :list="col.cards"
          group="cards"
          @start="drag = true"
          @end="drag = false"
          handle=".kanban__board__column__card__handle"
          @change="storeOrder"
        >
          <div
            class="kanban__board__column__card"
            v-for="(card, i) in col.cards"
            :key="i" @click="editItem(card)"
          >
            <div class="kanban__board__column__card__handle"></div>
            <div class="kanban__board__column__card__title">
              <h4>{{ card.title }}</h4>
            </div>
          </div>
        </draggable>
        <button class="kanban__board__add-card" @click="addItem(col.id)">Add Card</button>
      </div>
    </div>

    <modal name="item-form" height="auto">
      <form class="item-form" @submit.prevent="saveItem">
        <div class="item-form__title">
          <h3>{{ item.id ? "Edit" : "Add" }} Card</h3>
        </div>
        <div class="item-form__group">
          <label for="title" class="item-form__group__label">Title</label>
          <input type="text" class="item-form__group__input" v-model="item.title" id="title" name="title" placeholder="Title">

        </div>
        <div class="item-form__group">
          <label for="description" class="item-form__group__label">Description</label>
          <textarea name="description" class="item-form__group__input" v-model="item.description" id="description" cols="30" rows="10" placeholder="Description"></textarea>
        </div>

        <div class="item-form__footer">
          <div class="item-form__actions">
            <button class="item-form__cancel" @click="cancel" type="button">Cancel</button>
            <button class="item-form__submit" type="submit">Submit</button>
          </div>
        </div>
      </form>
    </modal>

    <modal name="column-form" height="auto">
      <form class="column-form" @submit.prevent="saveColumn">
        <div class="column-form__title">
          <h3>{{ column.id ? "Edit" : "Add" }} Column</h3>
        </div>
        <div class="column-form__group">
          <label for="colum_title" class="column-form__group__label">Title</label>
          <input type="text" class="column-form__group__input" v-model="column.title" id="colum_title" name="title" placeholder="Title">

        </div>

        <div class="column-form__footer">
          <div class="column-form__actions">
            <button class="column-form__cancel" @click="cancelColumn" type="button">Cancel</button>
            <button class="column-form__submit" type="submit">Submit</button>
          </div>
        </div>
      </form>
    </modal>
    <modal name="column-delete" height="auto">
      <form class="column-form" @submit.prevent="submitDeleteColumn">
        <div class="column-form__title">
          <h3>Are you sure you want to delete this column</h3>
        </div>
        

        <div class="column-form__footer">
          <div class="column-form__actions">
            <button class="column-form__cancel" @click="cancelColumn" type="button">Cancel</button>
            <button class="column-form__submit" type="submit">Delete</button>
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
      item:{
        id: null,
        title: "",
        description: "",
        column_id: null,
      },
      column:{
        id:null,
        title: ""
      },
      orderTimeout:0,
    };
  },
  
  mounted(){
    this.getColumns();
  },
  methods:{
    getColumns(){
      axios.get('/api/columns').then(({data}) => {
        this.columns = data;
      });
    },
    addColumn(){
      let id, title;
      this.column = {id,title, cards: []};
      this.$modal.show('column-form');
    },
    editColumn({id, title}){
      this.column = {id, title}
      this.$modal.show('column-form');
    },
    saveColumn(){
      axios.post('api/columns', this.column).then((response)=>{
        this.getColumns();
        this.$modal.hide('column-form');
      });
    },
    cancelColumn(){
      this.$modal.hide('column-form');
    },
    deleteColumn({id, title}){
      this.column = {id, title}
      this.$modal.show('column-delete');
    },
    submitDeleteColumn(){
      axios.delete(`api/columns/${this.column.id}`).then((response) => {
        this.getColumns();
        this.$modal.hide('column-delete');
      })
    },
    editItem({id,title, description, column_id}){
      this.item = {id,title, description, column_id};
      this.$modal.show('item-form');
    },
    addItem(column_id){
      let id, title, description;
      this.item = {id,title, description, column_id};
      this.$modal.show('item-form');
    },
    cancel(){

      this.$modal.hide('item-form');
    },
    saveItem(){
      let index = this.columns.findIndex(c => c.id == this.item.column_id);
      axios.post('api/cards', this.item).then((response) => {
        this.getColumns();
        this.$modal.hide('item-form');
      })
    },
    storeOrder(event){
      console.log(event);
      if(!!event.removed || !!event.moved){
        let order = [];
        this.columns.forEach(col => {
          order.push({
            column_id: col.id,
            cards : col.cards.map(c => c.id)
          });
        })
        axios.post('/api/cards/reorder', {order}).then(response => {
          this.getColumns();
        })
      }
    }
  }
};
</script>
