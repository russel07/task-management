<template>
  <div class="add-project">
    <div class="card col-md-10 offset-1">
      <div class="card-header bg-transparent">
        <h4>Add New Project</h4>
      </div>

      <div class="card-body">
        <form class="add-project" id="add_project">
          <div class="form-group row">
            <label for="project_code" class="col-md-3">Project Code</label>

            <div class="col-md-9">
              <input class="form-control" name="project_code" id="project_code" v-model="project_code" required/>
            </div>
          </div>

          <div class="form-group row">
            <label for="project_name" class="col-md-3">Project Name</label>

            <div class="col-md-9">
              <input class="form-control" name="project_name" id="project_name" v-model="project_name" required/>
            </div>
          </div>

          <div class="form-group text-center">
            <button type="button" class="btn btn-outline-primary" @click="saveProject">Create</button>
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
        project_code: '',
        project_name: ''
      }
    },
    created: function () {

    },
    methods: {
      saveProject: function () {
        let root = this;
        let data = {
          'action': 'wptms_create_project',
          'nonce': ajax_object.nonce,
          'project_name' : this.project_name,
          'project_code' : this.project_code
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
              router.push({name: 'Projects'})
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