<template>
  <div class="manage-projuct">
    <div class="card col-md-10 offset-1">
      <div class="card-header bg-transparent">
        <h4 class="card-title">Manage projects</h4>
      </div>

      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr class="text-center">
              <th>#</th>
              <th>Project Code</th>
              <th>Project Name</th>
              <th>Status</th>

              <th width="22%">
                <a class="btn btn-sm btn-outline-primary" href='#/add-project'>Add New</a>
              </th>

            </tr>
          </thead>

          <tbody>
            <tr v-for="(item, index) in projects" :key="index">
              <td class="text-center">{{ index +1}}</td>
              <td>{{ item.project_code}}</td>
              <td>{{ item.project_name}}</td>
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
  name: 'Projects',
  data() {
    return {
      projects: [],
      assets: ''
    }
  },
  created: function () {
    this.assets = ajax_object.plugin_assets;
    this.getProjects();
  },
  methods: {
    getEditLink: function(id){
      return '#/edit-project/'+id;
    },
    getImgLink: function(status){
      return this.assets+'images/'+status+'.png';
    },
    getProjects: function () {
      let root = this;
      $.ajax({
        url: ajaxurl,
        type: 'GET',
        dataType: 'json',
        data: {
          'action': 'get_projects',
          'nonce': ajax_object.nonce
        },
        beforeSend: function () {

        },
        success : function(data) {
          root.projects = data;
        },
        error : function(request,error)
        {
          console.log(error);
        }
      });
    },
    changeStatus: function (id){
      if(confirm('Are you sure you want to change status?')){
        let index = this.projects.findIndex(row => row.id === id);
        let row = this.projects[index];

        row.status = (row.status  === 'Active') ? 'Inactive' : 'Active';

        let root = this;

        $.ajax({
          url: ajaxurl,
          type: 'GET',
          dataType: 'json',
          data: {
            'action': 'change_project_status',
            'id': id,
            'nonce': ajax_object.nonce
          },
          beforeSend: function () {

          },
          success : function(data) {
            let index = root.projects.findIndex(row => row.id === id);
            root.projects.splice(index, 1, row);

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
    deleteProject: function (id){
      if(confirm('Are you sure you want to delete this item?')){

      }
    }
  }
}
</script>