<template>

  <div class="allocate-tasks">
    <div class="card col-md-10 offset-1">
      <div class="card-header bg-transparent">
        <h4>Allocate Project to User</h4>
      </div>

      <div class="card-body">
        <div class="card col-md-8 offset-2">
          <div class="card-body">
            <form class="allocate-task">
              <div class="form-group row">
                <label for="user_id" class="col-md-3">Select User</label>
                <div class="col-md-9">
                  <select class="form-control" name="userId" v-model="userId" id="user_id" @change="getProjects">
                    <option v-for="(item, index) in users" :value="item.ID" :key="index">{{ item.display_name }}</option>
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
                <th>Project Name</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              </thead>

              <tbody>
              <tr v-for="(item, index) in projects" :key="index">
                <td>{{ index+1 }}</td>
                <td>{{ item.project_name }}</td>
                <td>{{ item.allocated_status }}</td>
                <td>
                  <button class="btn btn-link" data-id="{{item.id}}" @click="changeStatus(item)">
                    <img :src='getImageLink(item.allocated_status)' :alt="item.allocated_status" :title="item.allocated_status"/></button>
                  <button v-if="item.allocated_status" class="ml-2 btn btn-sm btn-outline-danger" @click="deleteProject(item.id, item.allocated_id)">Delete</button></td>
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
  export default {
    data() {
      return {
        assets: '',
        userId: '',
        users: [],
        projects: []
      }
    },
    created: function () {
      this.assets = ajax_object.plugin_assets;
      this.getUsers();
    },
    methods: {
      getImageLink: function(status){
        status = (status === '' || status === undefined || (status === 'Deallocated')) ? 'inactive' : 'active';
        return this.assets+'images/'+status+'.png';
      },
      getUsers: function () {
        let data = {
          action: 'wptms_get_active_users',
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
            root.users = data;
          },
          error : function(request,error)
          {
            console.log("error");
          }
        });
      },
      getProjects: function (e) {
        this.userId = e.target.value;
        let data = {
          action: 'wptms_get_project_by_user',
          nonce: ajax_object.nonce,
          userId:this.userId
        }, root = this;

        if(this.userId !== ''){
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: data,
            beforeSend: function () {

            },
            success : function(data) {
              if(data.status){
                root.projects = data.projects;
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
          action: 'wptms_allocate_deallocate_project',
          nonce: ajax_object.nonce,
          user_id: this.userId,
          project_code: item.project_code,
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
              let index = root.projects.findIndex(row => row.id === item.id);
              root.projects[index] = data.data;
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
      deleteProject: function (project_id, id) {
        if (confirm("Are you sure you want to delete?")) {
          let data = {
            action: 'wptms_remove_project_from_user',
            nonce: ajax_object.nonce,
            project_id: project_id,
            id: id,
          }, root = this;

          $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: data,
            beforeSend: function () {

            },
            success: function (data) {
              if (data.status) {
                let index = root.projects.findIndex(row => row.id === project_id);

                root.projects[index] = data.data;
                root.$toast.show('Success!! ' + data.message, {
                  type: 'success'
                });
              } else {
                root.$toast.show('Error!! ' + data.message, {
                  type: 'error'
                });
              }

            },
            error: function (request, error) {
              console.log("error");
            }
          });
        }
      }
    }
  }
</script>