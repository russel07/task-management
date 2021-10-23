<?php
if(isset($data)):
    $reamining = $data->annual_leave_allocated - $data->annual_leave_taken;
    ?>
<nav class="navbar navbar-expand-sm bg-light mb-4">
    <ul class="navbar-nav col-md-10 offset-1">
        <li class="nav-item pr-5">
            <span><b>Bank Holiday:</b> <span id="overview_bank_holiday"><?php echo $data->bank_holiday; ?></span></span>
        </li>
        <li class="nav-item pr-5">
            <span><b>Leave Taken:</b> <span id="overview_leave_taken"><?php echo $data->annual_leave_taken; ?></span></span>
        </li>

        <li class="nav-item pr-5">
            <span><b>Leave Remaining:</b> <span id="overview_leave_remaining"><?php echo $reamining; ?></span></span>
        </li>
        <li class="nav-item">
            <span><b>Sick Leave:</b> <span id="overview_sick_leave"><?php echo $data->sick_leave; ?></span></span>
        </li>
    </ul>
</nav>

<?php else:?>

<nav class="navbar navbar-expand-sm bg-light mb-4">
    <ul class="navbar-nav col-md-10 offset-1">
        <li class="nav-item pr-5">
            <b>Bank Holiday:</b> 0
        </li>
        <li class="nav-item pr-5">
            <b>Annual Leave Taken:</b> 0
        </li>

        <li class="nav-item pr-5">
            <b>Annual Leave Remaining:</b> 20
        </li>
        <li class="nav-item">
            <b>Sick Leave:</b>0
        </li>
    </ul>
</nav>
<?php endif;?>