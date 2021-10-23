<template>
  <div class="allocate-tasks">
    <div class="card col-md-10 offset-1">
      <div class="card-header bg-transparent">
        <h4>Allocate Task to Project</h4>
      </div>

      <div class="card-body">
        <div class="card col-md-8 offset-2">
          <div class="card-body">
            <form class="allocate-task">
              <div class="form-group row">
                <label for="project_name" class="col-md-3">Select Project</label>
                <div class="col-md-9">
                  <select class="form-control" name="project_name" v-model="project_code" id="project_name" @change="getTaskList">
                    <option v-for="(item, index) in projects" :value="item.project_code" :key="index">{{ item.project_name }}</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="card col-md-8 offset-2">
          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Task Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="(item, index) in tasks" :key="index">
                  <td>{{ index+1 }}</td>
                  <td>{{ item.task_name }}</td>
                  <td>{{ item.allocated_status }}</td>
                  <td>
                    <button class="btn btn-link" data-id="{{item.id}}" @click="changeStatus(item)">
                      <img :src='getImgLink(item.allocated_status)' :alt="item.allocated_status" :title="item.allocated_status"/></button>
                    <button v-if="item.allocated_status" class="ml-2 btn btn-sm btn-outline-danger" @click="deleteFromProject(item.id, item.allocated_id)">Delete</button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import router from "../../router";

export default {
  data(){
    return {
      assets: '',
      project_code: '',
      projects: [],
      tasks: []
    }
  },
  created: function () {
    this.assets = ajax_object.plugin_assets;
    this.projectList();
  },
  methods: {
    getImgLink: function(status){
      status = (status === '' || status === undefined || (status === 'Deallocated')) ? 'inactive' : 'active';
      return this.assets+'images/'+status+'.png';
    },
    projectList: function () {
      let data = {
        action: 'get_active_projects',
        nonce: ajax_object.nonce
      }, root = this;

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function () {

        },
        success : function(data) {
          if(data.status){
            root.projects = data.data;
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
    },
    getTaskList: function (e) {
      this.project_code = e.target.value;
      let data = {
        action: 'wptms_get_task_by_project',
        nonce: ajax_object.nonce,
        project_code: this.project_code
      }, root = this;

      if(this.project_code !== ''){
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          dataType: 'json',
          data: data,
          beforeSend: function () {

          },
          success : function(data) {
            if(data.status){
              root.tasks = data.tasks;
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
    },
    changeStatus: function (item) {
      let data = {
        action: 'wptms_allocate_deallocate_task',
        nonce: ajax_object.nonce,
        project_code: this.project_code,
        task_code: item.task_code,
        allocated_status: item.allocated_status === undefined ? 'Allocate' :  item.allocated_status,
        allocated_id: item.allocated_id === undefined ? 0 : item.allocated_id,
      }, root = this;

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function () {

        },
        success : function(data) {
          if(data.status){
            let index = root.tasks.findIndex(row => row.id === item.id);
            root.tasks[index] = data.data;
            root.$toast.show('Success!! '+ data.message, {
              type: 'success'
            });
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
    },
    deleteFromProject: function (task_id, id) {
      if(confirm("Are you sure you want to delete?")){
        let data = {
          action: 'wptms_delete_task_from_project',
          nonce: ajax_object.nonce,
          task_id: task_id,
          id: id,
        }, root = this;

        $.ajax({
          url: ajaxurl,
          type: 'POST',
          dataType: 'json',
          data: data,
          beforeSend: function () {

          },
          success : function(data) {
            if(data.status){
              let index = root.tasks.findIndex(row => row.id === task_id);

              root.tasks[index] = data.data;
              root.$toast.show('Success!! '+ data.message, {
                type: 'success'
              });
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
}
</script>