jQuery(document).ready(function ($){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    $(document).on('click', "#wptms_submit_leave_request", function (e){
        let leave_type = $("#leave_type").val();
        let leave_day = $("#leave_day").val();

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'GET',
            dataType: 'json',
            data: {
                'action': 'save_leave_request',
                'leave_type': leave_type,
                'leave_day': leave_day,
                'nonce': ajax_object.nonce
            },
            beforeSend: function () {

            },
            success : function(data) {
                if(data.status)
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    })
                else
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    })
            },
            error : function(request,error)
            {
                Toast.fire({
                    icon: 'error',
                    title: "Please make sure you are loggedin"
                })
            }
        });
    })

    $(document).on('change', '.spend_hour', function (e) {
        let date = $(this).parent().data('date');
        let index = $(this).parent().data('index');
        let taskSelector = $(this).parent().parent().children('td:first'),task = $(this).parent().parent().children('td:first').find('select').val();
        let hourSelector =  $(this), hour = hourSelector.val();
        let taskDetailsSelector = $("textarea#task_details_"+index), task_details = taskDetailsSelector.val();
        let id = $(this).data('id');

        let data = {
            id, date, task, hour, task_details, 'action': 'save_task', 'nonce': ajax_object.nonce
        }
        console.log(data);

        call_ajax(data, taskSelector,  hourSelector, taskDetailsSelector);
    });

    $(document).on('blur', '.task_details', function (e) {
        let date = $(this).parent().data('date');
        let index = $(this).parent().data('index');
        let taskDetailsSelector = $(this).parent(), task_details = $(this).val();
        let taskSelector = $(this).parent().parent().prev().children('td:first'), task = $(this).parent().parent().prev().children('td:first').find('select').val();
        let hourSelector = $("select#spend_hour_"+index), hour = hourSelector.val();
        let id = $(this).data('id');

       let data = {
           id, date, task, hour, task_details, 'action': 'save_task', 'nonce': ajax_object.nonce
       }
       console.log(data);

        call_ajax(data, taskSelector,  hourSelector, taskDetailsSelector);
    });

    $(document).on('dblclick', '.edit-task', function () {
        let task_details = $(this).text();
        let id = $.trim($(this).data('index'));
        let tdth = $.trim($(this).attr('tr-child'));
        let hour = $.trim($(this).parent().prev().find("td:eq("+tdth+")").text());

        let hours = "<select id='spend_hour_"+id+"' class='form-control spend_hour' data-id='"+id+"'>"+
        "<option value=''>Select</option>";

        if(hour === '4:00')
            hours += "<option value='4:00' selected>4:00</option>";
        else hours += "<option value='4:00'>4:00</option>";

        if(hour == '5:00')
            hours += "<option value='5:00' selected>5:00</option>";
        else hours += "<option value='5:00'>5:00</option>";

        if(hour == '6:00')
            hours += "<option value='6:00' selected>6:00</option>";
        else hours += "<option value='6:00'>6:00</option>";

        if(hour == '7:00')
            hours += "<option value='7:00' selected>7:00</option>";
        else hours += "<option value='7:00'>7:00</option>";

        if(hour == '8:00')
            hours += "<option value='8:00' selected>8:00</option>";
        else hours += "<option value='8:00'>8:00</option>";
        hours += "</select>";

        $(this).parent().prev().find("td:eq("+tdth+")").html(hours);
        $(this).html("<textarea name='task_details' class='form-control task_details' id='task_details_"+id+"' data-id='"+id+"'>"+task_details+"</textarea>")
    });

    $(document).on('dblclick', '.edit-hour', function () {
        let hour = $.trim($(this).text());
        let id = $.trim($(this).data('index'));
        let tdth = $.trim($(this).attr('tr-child'));
        let task_details = $.trim($(this).parent().next().find("td:eq("+tdth+")").text());

        let hours = "<select id='spend_hour_"+id+"' class='form-control spend_hour' data-id='"+id+"'>"+
            "<option value=''>Select</option>";

        if(hour === '4:00')
            hours += "<option value='4:00' selected>4:00</option>";
            else hours += "<option value='4:00'>4:00</option>";

        if(hour == '5:00')
            hours += "<option value='5:00' selected>5:00</option>";
        else hours += "<option value='5:00'>5:00</option>";

        if(hour == '6:00')
            hours += "<option value='6:00' selected>6:00</option>";
        else hours += "<option value='6:00'>6:00</option>";

        if(hour == '7:00')
            hours += "<option value='7:00' selected>7:00</option>";
        else hours += "<option value='7:00'>7:00</option>";

        if(hour == '8:00')
            hours += "<option value='8:00' selected>8:00</option>";
        else hours += "<option value='8:00'>8:00</option>";
        hours += "</select>";

        $(this).html(hours);
        $(this).parent().next().find("td:eq("+tdth+")").html("<textarea name='task_details' class='form-control task_details' id='task_details_"+id+"' data-id='"+id+"'>"+task_details+"</textarea>")
    });

    function call_ajax(data, taskSelector,  hourSelector, taskDetailsSelector){
        //console.log(hourSelector.parent().parent().html());return false;
        if(data.date !== '' && data.task !== '' && data.hour !== '' && data.task_details !== ''){
            if(data.id !== undefined){
                data.action = 'update_task'
            }
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'GET',
                dataType: 'json',
                data: data,
                beforeSend: function () {

                },
                success : function(data) {
                    if(data.status) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        });
                        updateHolidayOverview(data.overview);
                        hourSelector.parent().text(data.row.spend_hour)
                        taskDetailsSelector.text(data.row.task_details)
                    }
                    else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message
                        })

                        return false;
                    }
                },
                error : function(request,error)
                {
                    Toast.fire({
                        icon: 'error',
                        title: "Please make sure you are loggedin"
                    })
                }
            });
        }
        else if(data.id !== undefined  && (data.hour === '' || data.task_details === '')){
            if(confirm("Do you really want to delete?")){
                data.action = 'delete_task';

                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'GET',
                    dataType: 'json',
                    data: data,
                    beforeSend: function () {

                    },
                    success : function(data) {
                        if(data.status) {
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            });
                            updateHolidayOverview(data.overview);

                            let row = data.row;
                        }
                        else {
                            Toast.fire({
                                icon: 'error',
                                title: data.message
                            })

                            return false;
                        }
                    },
                    error : function(request,error)
                    {
                        Toast.fire({
                            icon: 'error',
                            title: "Please make sure you are loggedin"
                        })
                    }
                });
            }else{
                console.log('cancle');
            }
        }
        return false;
    }

    function updateHolidayOverview(overview){
        $("span#overview_bank_holiday").text(overview.bank_holiday);
        $("span#overview_leave_taken").text(overview.annual_leave_taken);
        $("span#overview_leave_remaining").text(parseFloat(overview.annual_leave_allocated) - parseFloat(overview.annual_leave_taken));
        $("span#overview_sick_leave").text(overview.sick_leave);
    }
})