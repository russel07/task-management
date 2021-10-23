<div class="card col-md-12">
    <div class="card-header bg-transparent">
        <p class="card-title">Submit Leave request</p>
    </div>
    <div class="card-body">
        <form id="leave_request_form">
            <div class="form-group">
                <label for="leave_type">Leave Type</label>
                <select name="leave_type" id="leave_type" class="form-control">
                    <option value="">Select</option>
                    <option value="annual_leave">Annual Leave</option>
                    <option value="sick_leave">Sick Leave</option>
                </select>
            </div>

            <div class="form-group">
                <label for="leave_day">Leave Days</label>
                <select name="leave_day" id="leave_day" class="form-control">
                    <option value="">Select</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>

            <div class="form-group text-center">
                <input type="submit" id="wptms_hidden_submit_leave_request" style="display: none;"/>
                <button type="button" class="btn btn-outline-primary" id="wptms_submit_leave_request">Submit</button>
            </div>

        </form>
    </div>
</div>