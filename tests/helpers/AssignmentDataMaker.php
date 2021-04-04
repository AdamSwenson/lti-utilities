<?php


namespace Tests\helpers;

use App\Models\Assignment;
use App\Models\Activity;
use App\Models\TaskInput;
use App\Models\Tasks\AuthorTask;
use App\Models\Tasks\Task;
use App\Models\User;

//use App\User;
//use Illuminate\Support\Facades\Auth;


/**
 * Class AssignmentDataMaker
 * Creates test data for relationships between tasks in
 * an activity
 *
 * @package Tests\helpers
 */
class AssignmentDataMaker
{

    /**
     * Creates a test ass
     * @param Activity $activity
     * @param $numberSteps
     * @param $tasksPerStep
     */
    static public function populateActivity(Activity $activity, $numberSteps, $tasksPerStep){

        //Make sure activity has been initialized without wiping out existing
        if(is_null($activity->getAssignmentRoot())){
            $activity->initializeAssignmentRoot();
        }

        for ($step=0;$step<$numberSteps;$step++){
            $stepAssign = Assignment::factory()->create(['activity_id' => $activity->id]);
            $activity->getAssignmentRoot()->addChild($stepAssign, $step);

            for($task=0;$task<$tasksPerStep;$task++){

                $authorTask = Task::factory()->create();
                $authorAssign = $activity->assignAuthorTask($authorTask, $stepAssign, $task);

//                $authorAssign = Assignment::factory()->create(['activity_id' => $activity->id]);
//                $stepAssign->addChild($authorAssign, $task);

                for($reviewTask=0;$reviewTask<$tasksPerStep;$reviewTask++){
                    //Add reviewing tasks to the author task
                    $reviewerTask = Task::factory()->create();
                    $reviewerAssign = $activity->assignReviewerTask($reviewerTask, $authorAssign, $reviewTask);

//                    $reviewAssign = Assignment::factory()->review()->create(['activity_id' => $activity->id]);
//                    $authorAssign->addChild($reviewAssign, $reviewTask);

                }

            }

        }

    }


    /**
     * Creates a task input for all author task assignments associated with the activity.
     * @param Activity $activity
     * @param User $user
     */
    static public function populateActivityWithAuthorInputs(Activity $activity, User $user){
        $assignments = $activity->assignments()->where('is_reviewing_task', false)->get();
        foreach ($assignments as $assignment) {
            TaskInput::factory()->create([
                'assignment_id' => $assignment->id,
                'user_id' => $user->id]);
        }

    }


    /**
     * Creates a task input for all reviewer task assignments associated with the activity.
     * @param Activity $activity
     * @param User $user
     */
    static public function populateActivityWithReviewerInputs(Activity $activity, User $user){
        $assignments = $activity->assignments()
            ->where('is_reviewing_task', true)
            ->get();
        foreach ($assignments as $assignment) {
            TaskInput::factory()->create([
                'assignment_id' => $assignment->id,
                'user_id' => $user->id]);
        }

    }



    static public function addChild(Assignment $assignment, Activity $activity, $position)
    {
        $task = Task::factory()->create();
        $assign = Assignment::create([
            'activity_id' => $activity->id,
            'task_id' => $task->id
        ]);
        return $assignment->addChild($assign, $position, true);

    }


    /**
     * Creates a full tree of tasks for the given activity
     * Returns the root Assignment object (whose parentId is null)
     *
     * @param $activity
     * @param int $numLevels
     * @param int $numChildren
     * @return mixed
     */
    static public function makeActivityData($activity, $numLevels = 3, $numChildren = 3, $save=true)
    {
        $rootTask = Task::factory()->create(); //standin for activity1
        $parentAssignment = Assignment::create([
            'activity_id' => $activity->id,
            'task_id' => $rootTask->id
        ]);

        function rf($assignment, $activity, $numChildren, $numLevels, $level, $save)
        {
            for ($h = 0; $h < $numChildren; $h++) {
                //todo added the position variable when bug fixing. need to check that didn't mess things up. [added later: may be related to PWB-28]
                $child = AssignmentDataMaker::addChild($assignment, $activity, $h);

                //if we aren't as deep as we need to go,
                //repeat everything for the child

                if($save){
                    $assignment->push();
                    $child->save();
                }

                $level += 1;
                if ($level <= $numLevels) {
                    rf($child, $activity, $numChildren, $numLevels, $level, $save);
                }
            }
        }

        rf($parentAssignment, $activity, $numChildren, $numLevels, 0, $save);
        return $parentAssignment;
    }


    static public function makeOrderJsonData(Activity $activity, $numLevels = 3, $numAtLevel = 3)
    {
        $order = [];

        if (!$activity) Activity::factory()->create();

        $root = Task::factory()->create(); //standin for activity1

        for ($level = 0; $level < $numLevels; $level++) {

            for ($h = 0; $h < $numAtLevel; $h++) {
                $task = Task::factory()->create();
                $order[] = [
                    'activityId' => $activity->id,
                    'parentId' => $root->id,
                    'taskId' => $task->id,
                    'taskOrder' => $h];
            }
            //On the last time through, we skip
            //Otherwise, we make children
            if ($level < $numLevels) {
                for ($j = 0; $j < $numAtLevel; $j++) {
                    $child = Task::factory()->create();
                    $order[] = [
                        'activityId' => $activity->id,
                        'parentId' => $task->id,
                        'taskId' => $child->id,
                        'taskOrder' => $j];
                }
            }
        }
        return $order;
    }


}
