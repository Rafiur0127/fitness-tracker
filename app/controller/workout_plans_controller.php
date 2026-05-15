<?php
require_once __DIR__ . '/../model/workout_plans_model.php';

function handle_workout_plans($pdo, $user_id) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["create_plan"])) {
            create_plan($pdo, $_POST["name"], $_POST["weeks"], $_POST["description"]);
        }

        if (isset($_POST["assign_schedule"])) {
            assign_schedule($pdo, $user_id, $_POST["program_id"], $_POST["day"], $_POST["activity"]);
        }

        if (isset($_POST["delete_schedule"])) {
            delete_schedule($pdo, $user_id, $_POST["schedule_id"]);
        }
    }

    $programs = get_all_programs($pdo);
    $schedule = get_user_schedule($pdo, $user_id);
    return ["programs" => $programs, "schedule" => $schedule];
}
