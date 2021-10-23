<template>
  <div class="employee">
    <div class="card col-md-8 offset-2">
      <div class="card-header">
        <h1 class="card-title">View profile for: {{employee.employee_name}}</h1>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-4">Username ID</div>
          <div class="col-md-8">{{employee.ID}}</div>
        </div>

        <div class="row">
          <div class="col-md-4">Username</div>
          <div class="col-md-8">{{employee.user_login}}</div>
        </div>

        <div class="row">
          <div class="col-md-4">Nick Name</div>
          <div class="col-md-8">{{employee.user_nicename}}</div>
        </div>

        <div class="row">
          <div class="col-md-4">Email Address</div>
          <div class="col-md-8">{{employee.user_email}}</div>
        </div>

        <div class="row">
          <div class="col-md-4">Display Name</div>
          <div class="col-md-8">{{employee.display_name}}</div>
        </div>

        <div class="row">
          <div class="col-md-4">Designation</div>
          <div class="col-md-8">{{employee.designation}}</div>
        </div>

        <div class="row">
          <div class="col-md-4">Join Date</div>
          <div class="col-md-8">{{employee.join_date}}</div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'ViewEmployee',
    data (){
      return{
        employee: []
      }
    },
    created: function () {
      console.log("created");
      this.getEmployee()
    },
    methods: {
      getEmployee: function (){
        let id = this.$route.params.employeeId;

        let root = this;
        $.ajax({
          url: ajaxurl,
          type: 'GET',
          dataType: 'json',
          data: {
            'action': 'get_employee',
            'id': id
          },
          beforeSend: function () {

          },
          success : function(data) {
            console.log("employee");
            console.log(data);
            root.employee = data;
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