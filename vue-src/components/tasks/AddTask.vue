<template>
  <div class="add-task">
    <div class="card col-md-10 offset-1">
      <div class="card-header bg-transparent">
        <h4>Add New Task</h4>
      </div>

      <div class="card-body">
        <form class="add-task" id="add_task">
          <div class="form-group row">
            <label for="task_code" class="col-md-3">Task Code</label>
            <div class="col-md-9">
              <input type="text" name="task_code" class="form-control" id="task_code" v-model="task_code">
            </div>
          </div>

          <div class="form-group row">
            <label for="task_name" class="col-md-3">Task Name</label>
            <div class="col-md-9">
              <input type="text" name="task_name" class="form-control" id="task_name" v-model="task_name">
            </div>
          </div>

          <div class="form-group text-center">
            <button type="button" class="btn btn-outline-primary" @click="createTask">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import router from "../../router";

export default {
  data(){
    return {
      task_code: '',
      task_name: ''
    }
  },
  created: function () {

  },
  methods: {
    createTask: function () {
      let root = this;

      let data = {
        'action': 'wptms_create_task',
        'nonce': ajax_object.nonce,
        task_code: this.task_code,
        task_name: this.task_name
      }

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function () {

        },
        success : function(data) {
          if(data.status){
            root.$toast.show('Success!! '+ data.message, {
              type: 'success'
            });
            router.push({name: 'Tasks'})
          }else{
            root.$toast.show('Error!! '+ data.message, {
              type: 'error'
            });
          }

        },
        error : function(request,error)
        {
          console.log("error");
        }
      });
    }
  }
}
</script>