<template>
  <div class="add-holiday">
    <div class="card col-md-8 offset-2">
      <div class="card-header bg-white">
        <h1 class="card-title">Add Holiday</h1>
      </div>

      <div class="card-body">
        <form id="wptms_add_holiday">
          <div class="form-group row">
            <label for="user" class="col-md-3">Select User</label>
            <div class="col-md-9">
              <select class="form-control" id="user" name="user_id" v-model="user_id">
                <option v-for="(user, index) in users" :value="user.user_id">{{ user.name }}</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="annual_leave" class="col-md-3">Allocated Leave</label>
            <div class="col-md-9">
              <input type="number" name="bank_holiday" class="form-control" v-model="annual_leave_allocated"/>
            </div>
          </div>

          <div class="form-group text-center">
            <button type="button" class="btn btn-outline-primary" @click="saveHoliday">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import router from '../../router'
  export default {
    name: 'AddHoliday',
    data(){
      return{
        annual_leave_allocated: '',
        user_id: 0,
        users: []
      }
    },
    created: function () {
      this.getUser();
    },
    methods: {
      getUser: function () {
        let root = this;
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          dataType: 'json',
          data: {
            'action': 'get_user_list',
            'nonce': ajax_object.none
          },
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
      saveHoliday: function (){
        let root = this;
        let data = {
          'action': 'insert_holiday',
          'user_id': this.user_id,
          'annual_leave_allocated': this.annual_leave_allocated
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
              router.push({name: 'HolidayOverview'})
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