<template>
  <div class="employee">
    <div class="row">
      <div class="card col-md-10 offset-1">
        <div class="card-header bg-transparent">
          <h1 class="card-title">Manage Employee</h1>
        </div>

        <div class="card-body">
          <table class="table table-bordered table-hover table-responsive">
            <thead>
              <tr>
                <th>Username</th>
                <th>Email Address</th>
                <th>Display Name</th>
                <th>Full Name</th>
                <th>Designamtion</th>
                <th>Join Date</th>
                <th width="22%">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(item, index) in employees" :key="index">
                <td>{{item.user_login}}</td>
                <td>{{item.user_email}}</td>
                <td>{{item.display_name}}</td>
                <td>{{item.employee_name}}</td>
                <td>{{item.designation}}</td>
                <td>{{item.join_date}}</td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" :href=getViewLink(item.employee_id) >View</a>&nbsp;
                  <a class="btn btn-sm btn-outline-warning" :href="getEditLink(item.id)">Edit</a>&nbsp;
                  <button class="ml-2 btn btn-sm btn-outline-danger" @click="deleteProduct(item.id)">Delete</button>
                </td>
              </tr>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Employee',

    data() {
      return {
        employees: []
      }
    },
    created: function(){
      console.log("dom created");
      this.getEmployees();
    },
    methods: {
      getViewLink: function (id){
        return "#/view-employee/"+id;
      },
      getEditLink: function(id) {
        return "#/edit-employee/"+id;
      },
      getEmployees: function (){
        let root = this;
        $.ajax({
          url: ajaxurl,
          type: 'GET',
          dataType: 'json',
          data: {
            'action': 'get_employees'
          },
          beforeSend: function () {

          },
          success : function(data) {
            root.employees = data;
          },
          error : function(request,error)
          {
            console.log(error);
          }
        });
      }
    }
  }
</script>