<?php
require_once __DIR__ . '/../model/workout_plans_model.php';

function handle_workout_plans($pdo, $user_id) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["create_plan"])) {
            $weeks = (int) ($_POST["weeks"] ?? 0);
            if ($weeks >= 4 && $weeks <= 12) {
                create_plan($pdo, trim($_POST["name"] ?? ''), $weeks, trim($_POST["description"] ?? ''));
            }
        }

        if (isset($_POST["assign_schedule"])) {
            assign_schedule($pdo, $user_id, (int) $_POST["program_id"], $_POST["day"], $_POST["activity"]);
        }

        if (isset($_POST["delete_schedule"])) {
            delete_schedule($pdo, $user_id, $_POST["schedule_id"]);
        }
    }

    $programs = get_all_programs($pdo);
    $schedule = get_user_schedule($pdo, $user_id);
    return ["programs" => $programs, "schedule" => $schedule];
}
