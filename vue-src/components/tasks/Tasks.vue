<template>
  <div class="manage-tasks">
    <div class="card col-md-10 offset-1">
      <div class="card-header bg-transparent">
        <h4 class="card-title">Manage Tasks</h4>
      </div>
      <div class="card-body">
        <table class="table table-striped table-hover table-bordered">
          <thead>
            <tr class="text-center">
              <th>#</th>
              <th>Task Code</th>
              <th>Task Name</th>
              <th>Status</th>
              <th width="22%">
                <a href="#/add-task" class="btn btn-outline-primary">Add new</a>
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) in tasks" :key="index">
              <td class="text-center">{{ index+1}}</td>
              <td>{{ item.task_code}}</td>
              <td>{{ item.task_name}}</td>
              <td class="text-center">{{ item.status}}</td>
              <td>
                <button class="btn btn-link" data-id="{{item.id}}" @click="changeStatus(item.id)">
                  <img :src='getImgLink(item.status)' :alt="item.status" :title="item.status"/></button>
                <a class="btn btn-sm btn-outline-warning" :href="getEditLink(item.id)" >Edit</a>&nbsp;
                <button class="ml-2 btn btn-sm btn-outline-danger" @click="deleteProject(item.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data(){
      return {
        tasks: [],
        assets: ''
      }
    },
    created: function () {
      this.assets = ajax_object.plugin_assets;
      this.getTasks();
    },
    methods: {
      getEditLink: function(id){
        return '#/edit-task/'+id;
      },
      getImgLink: function(status){
        return this.assets+'images/'+status+'.png';
      },
      getTasks: function () {
        let root = this;
        $.ajax({
          url: ajaxurl,
          type: 'GET',
          dataType: 'json',
          data: {
            'action': 'wptms_get_tasks',
            'nonce': ajax_object.nonce
          },
          beforeSend: function () {

          },
          success : function(data) {
            root.tasks = data;
          },
          error : function(request,error)
          {
            console.log(error);
          }
        });
      },
      changeStatus: function (id){
        if(confirm('Are you sure you want to change status?')){
          let index = this.tasks.findIndex(row => row.id === id);
          let row = this.tasks[index];

          row.status = (row.status  === 'Active') ? 'Inactive' : 'Active';

          let root = this;

          $.ajax({
            url: ajaxurl,
            type: 'GET',
            dataType: 'json',
            data: {
              'action': 'wptms_change_task_status',
              'id': id,
              'nonce': ajax_object.nonce
            },
            beforeSend: function () {

            },
            success : function(data) {
              let index = root.tasks.findIndex(row => row.id === id);
              root.tasks.splice(index, 1, row);

              root.$toast.show('Success!'+ data.message, {
                type: 'success'
              });

            },
            error : function(request,error)
            {
              console.log(error);
            }
          });
        }
      },
    }
  }
</script>