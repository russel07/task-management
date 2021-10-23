<template>
  <div class="employee">
    <div class="row">
      <div class="card col-md-10 offset-1">
        <div class="card-header bg-transparent">
          <h1 class="card-title">Holiday Overview</h1>
        </div>

        <div class="card-body">
          <table class="table table-bordered table-hover table-responsive">
            <thead>
            <tr>
              <th>Username</th>
              <th>Email Address</th>
              <th>Display Name</th>
              <th>Bank Holiday</th>
              <th>Sick Leave</th>
              <th>Allocated Leave</th>
              <th>Leave Taken</th>
              <th width="15%">
                <a class="btn btn-sm btn-outline-primary" href='#/add-holiday'>Add New</a>
              </th>
            </tr>
            </thead>

            <tbody>
              <tr v-for="(item, index) in overviews" :key="index">
                <td>{{item.user_login}}</td>
                <td>{{item.user_email}}</td>
                <td>{{item.display_name}}</td>
                <td>{{item.bank_holiday}}</td>
                <td>{{item.sick_leave}}</td>
                <td>{{item.annual_leave_allocated}}</td>
                <td>{{item.annual_leave_taken}}</td>
                <td>
                  <a class="btn btn-sm btn-outline-warning" :href="getEditLink(item.leave_id)">Edit</a>&nbsp;
                  <button class="ml-2 btn btn-sm btn-outline-danger" @click="deleteHoliday(item.leave_id)">Delete</button>
                </td>
              </tr>
            </tbody>

          </table>
        </div>
      </div>

      <div class="card col-md-10 offset-1">
        <div class="card-header bg-transparent">
          <h1 class="card-title">Available shortcode</h1>
        </div>

        <div class="card-body">
          <div class="form-group row">
            <label class="col-md-4" for="holiday_overview">Holiday Summary</label>
            <div class="col-md-8">
              <input class="form-control shortcode" name="holiday_overview" value="[WPTMS_SHORTCODE holiday_overview='true']" id="holiday_overview" @click="copyClipboard">
            </div>
          </div>


          <div class="form-group row">
            <label class="col-md-4" for="weekly_shee">Weekly Timesheet Form</label>
            <div class="col-md-8">
              <input class="form-control shortcode" name="weekly_shee" value="[WPTMS_SHORTCODE weekly_timesheet='true']" id="weekly_shee" @click="copyClipboard">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'HolidayOverview',

    data(){
      return{
        overviews: []
      }
    },

    created: function (){
      console.log("Created");
      this.getHolidayList();
    },
    methods: {
      getEditLink: function(id) {
        return "#/edit-holiday/"+id;
      },
      getHolidayList: function () {
        let root = this;
        $.ajax({
          url: ajaxurl,
          type: 'GET',
          dataType: 'json',
          data: {
            'action': 'get_holiday_list'
          },
          beforeSend: function () {

          },
          success : function(data) {
            root.overviews = data;
          },
          error : function(request,error)
          {
            console.log(error);
          }
        });
      },
      deleteHoliday(id){
        if(confirm('Are you sure you want to delete?')){
          let  root = this;
          let data = {
            id: id,
            action: 'delete_holiday'
          }

          $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: data,
            beforeSend: function () {
            },
            success : function(data) {
              let index = root.overviews.findIndex(row => row.leave_id === id);
              root.overviews.splice(index, 1);

              root.$toast.show('Success! Item deleted successfully', {
                type: 'success'
              });
            },
            error : function(request,error)
            {
              root.$toast.show('Error! Something went wrong, please try later', {
                type: 'error'
              });
            }
          });
        }
      },
      copyClipboard: function (e) {
        this.copyToClipboard(e.target.value)
      },
      copyToClipboard: function(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text;
        sampleTextarea.select();
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
      }
    }
  }
</script>