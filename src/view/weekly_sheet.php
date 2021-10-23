<?php
$url = $_SERVER['REQUEST_URI'];
$position = strpos($url, '?week=');
if($position)
    $myUri = substr($url, 0, $position).'/';
else $myUri = $url;

if(isset($data)): ?>
    <div class="row">
       <div class="col-md-6 text-left">
           <a href="<?php echo $myUri;?>?week=<?php echo $data['prevWeek']?>&year=<?php echo $data['prevYear']?>">&#60;&#60; Prev Week</a>
       </div>
       <div class="col-md-6 text-right">
           <a href="<?php echo $myUri;?>?week=<?php echo $data['nextWeek']?>&year=<?php echo $data['nextYear']?>">Next Week &#62;&#62;</a>
       </div>
    </div>
    <table class="table table-bordered table-striped table-responsive">
        <thead>
            <tr>
                <td class="text-center">
                    Date
                </td>
                <?php foreach ($data['week'] as $row):?>
                    <td class="text-center"><?php echo $row['date'];?> <hr/><br/> <?php echo $row['day']?></td>
                <?php endforeach;?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">
                    Total Hours
                </td>
                <?php foreach ($data['hours'] as $hour):?>
                    <td class="text-center"><?php echo $hour;?></td>
                <?php endforeach;?>
            </tr>
            <?php
                $_key = -1;
                if(isset($data['tasks']) && !empty($data['tasks'])):
                foreach ($data['tasks'] as $_key => $task): ?>
                <tr>
                    <td class="text-center" rowspan="2">
                        <select name="task_name" id="task_name_<?php echo $_key;?>" class="form-control task_name">
                            <option value="<?php echo $task['task_name'];?>"><?php echo \Russel\WpTms\Helper\appHelper::getTaskName($task['task_name']);?></option>
                        </select>
                    </td>
                    <?php
                        $i = 0;
                        foreach ($task['weekly_tasks'] as $key => $item):
                            if($item['hours'] !== ''): ?>
                                <td data-date="<?php echo $key;?>" class="text-center edit-hour" data-index="<?php echo $item['task_id'];?>" tr-child="<?php echo $i+1;?>"><?php echo $item['hours'];?> </td>
                            <?php else: ?>
                                <td data-date="<?php echo $key;?>" data-index="<?php echo $_key.$i;?>" tr-child="<?php echo $i+1;?>">
                                    <select id="spend_hour_<?php echo $_key.$i; ?>" class="form-control spend_hour">
                                        <option value="">Select</option>
                                        <option value="4:00">4:00</option>
                                        <option value="5:00">5:00</option>
                                        <option value="6:00">6:00</option>
                                        <option value="7:00">7:00</option>
                                    </select>
                                </td>
                            <?php endif;?>

                    <?php $i++; endforeach;?>
                </tr>
                <tr>
                    <?php
                    $i = 0;
                    foreach ($task['weekly_tasks'] as $key => $item):
                        if($item['hours'] !== ''): ?>
                            <td data-date="<?php echo $key;?>" class="text-center edit-task" data-index="<?php echo $item['task_id'];?>" tr-child="<?php echo $i+1;?>"><?php echo $item['task_details'];?></td>
                        <?php else: ?>
                            <td data-date="<?php echo $key;?>" data-index="<?php echo $_key.$i;?>" tr-child="<?php echo $i+1;?>">
                                <textarea id="task_details_<?php echo $_key.$i; ?>" class="form-control task_details"></textarea>
                            </td>
                        <?php endif;?>
                    <?php $i++; endforeach;?>
                </tr>
            <?php endforeach; endif;?>

            <tr>
                <td rowspan="2">
                    <select name="task_name" id="task_name" class="form-control task_name">
                        <option value="">Select</option>
                        <?php foreach($data['taskList'] as $_task){
                            echo "<option value='".$_task->task_code."'>".$_task->task_details."</option>";
                        } ?>
                    </select>
                </td>
                <?php foreach ($data['week'] as $i => $row):?>
                    <td data-date="<?php echo $row['date'];?>"  data-index="<?php echo ($_key+1).$i;?>" tr-child="<?php echo $i+1;?>">
                        <select id="spend_hour_<?php echo ($_key+1).$i; ?>" class="form-control spend_hour">
                            <option value="">Select</option>
                            <option value="4:00">4:00</option>
                            <option value="5:00">5:00</option>
                            <option value="6:00">6:00</option>
                            <option value="7:00">7:00</option>
                            <option value="8:00">8:00</option>
                        </select>
                    </td>
                <?php endforeach;?>
            </tr>

            <tr>
                <?php foreach ($data['week'] as $i => $row):?>
                    <td data-date="<?php echo $row['date'];?>" data-index="<?php echo ($_key+1).$i;?>" tr-child="<?php echo $i+1;?>">
                        <textarea id="task_details_<?php echo ($_key+1).$i; ?>" class="form-control task_details"></textarea>
                    </td>
                <?php endforeach;?>
            </tr>
        </tbody>
    </table>
<?php  endif;?>
