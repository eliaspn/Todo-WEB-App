import { getCookie } from './helpers';
var { Model, View, Collection, Router, LocalStorage } = Backbone;

var ENTER_KEY = 13; 
var TodoFilter = '';

class Todo extends Model {

  defaults() {
    return {
      title: '',
      completed: false,
      notes: '',
      user_id: getCookie('userId')
    };
  }

  toggle() {
    this.save({
      completed: !parseInt(this.get('completed'))
    });
  }
}

class TodoList extends Collection {

  constructor(options) {
    super(options);
    this.model = Todo;
    this.url = 'api/index.php/tasks'
  }

  completed() {
    return this.filter(todo => todo.get('completed'));
  }

}

var Todos = new TodoList(); // let

class TodoView extends View {

  constructor(options) {
    this.tagName = 'li';
    this.template = _.template($('#item-template').html());
    this.input = '';
    this.textarea = '';

    this.events = {
      'click .toggle': 'toggleCompleted',
      'dblclick label': 'edit',
      'click .destroy': 'clear',
      'keypress .edit': 'updateOnEnter',
      'blur .edit': 'close',
      //notes
      'dblclick .addNotes': 'addNotes',
      'blur .notesedit': 'closeNotes',

    };

    super(options);

    this.listenTo(this.model, 'change', this.render);
    this.listenTo(this.model, 'destroy', this.remove);

  }

  render() {
    this.$el.html(this.template(this.model.toJSON()));
    
    flag = (parseInt(this.model.get('completed')) == 1) ? true: false; 
    this.$el.toggleClass('completed',flag );
    this.input = this.$('.edit');
    this.textarea = this.$('.notesedit');
    return this;
  }



  // *Toggle the `'completed'` state of the model.*
  toggleCompleted() {
    this.model.toggle();
  }

  edit() {
    var value = this.input.val(); 

    this.$el.addClass('editing');
    this.input.val(value).focus();
  }

  addNotes() {
       this.$el.find('.notesedit').fadeIn().focus();
  }

  close() {
    var title = this.input.val();

    if (title) {
      this.model.save({ title });
    } else {
      this.clear();
    }

    this.$el.removeClass('editing');
  }

  closeNotes() {
    var notes = this.textarea.val();
    if (notes) {
      this.model.save({ notes });
    } else {
      this.clear();
    }
  }

  updateOnEnter(e) {
    if (e.which === ENTER_KEY) {
      this.close();
    }
  }

  clear() {
    this.model.destroy();
  }
}

export class AppView extends View {

  constructor() {

    this.setElement($('#todoapp'), true);


    // *Delegate events for creating new items and clearing completed ones.*
    this.events = {
      'keypress #new-todo': 'createOnEnter',
      'click #clear-completed': 'clearCompleted',
    };

    // *At initialization, we bind to the relevant events on the `Todos`
    // collection, when items are added or changed. Kick things off by
    // loading any preexisting todos that might be saved in localStorage.*
    this.$input = this.$('#new-todo');
    this.$main = this.$('#main');

    this.listenTo(Todos, 'add', this.addOne);
    this.listenTo(Todos, 'reset', this.addAll);
    this.listenTo(Todos, 'change:completed', this.filterOne);
    this.listenTo(Todos, 'filter', this.filterAll);
    this.listenTo(Todos, 'all', this.render);

    Todos.fetch({data: {'userId': getCookie('userId')}});

    super();
  }

  render() {
    if (Todos.length) {
      this.$main.show();
    } else {
      this.$main.hide();
    }
  }

  addOne(model) {
    var view = new TodoView({ model });
    $('#todo-list').append(view.render().el);
  }

  addAll() {
    this.$('#todo-list').html('');
    Todos.each(this.addOne, this);
  }

  filterOne(todo) {
    todo.trigger('visible');
  }

  newAttributes() {
    return {
      title: this.$input.val().trim(),
      completed: false,
      notes: ""
    };
  }

  createOnEnter(e) {
    if (e.which !== ENTER_KEY || !this.$input.val().trim()) {
      return;
    }

    Todos.create(this.newAttributes());
    this.$input.val('');
  }


}


class UserModel extends Model {
  constructor() {
     this.url = 'api/index.php/users';
     super();
  }

  defaults() {
    return {
      request_ip: '',
      is_active: '',
      signup_datetime: ''
    };
  }
}

var User = new UserModel();

export class Users extends View {
  constructor() {
    if (getCookie('userId')) {
      User.fetch({data: {userId: getCookie('userId')}});
    }else {
      User.save({},{success: function(col,resp) {
        document.cookie = 'userId='+resp.id;
      }});
    }
    super();
  }
}