<?php
session_start();
date_default_timezone_set("Asia/Manila");

require(__DIR__ . "/conn.php");
require(__DIR__ . "/helpers.php");

try {
  $helpers = new Helpers($conn, $_SESSION);

  if (isset($_GET["action"])) {

    switch ($_GET["action"]) {
      case "add_admin":
        add_admin();
        break;
      case "change_password":
        change_password();
        break;
      case "delete_profile":
        delete_profile();
        break;
      case "delete_data":
        delete_data();
        break;
      case "profile_save":
        profile_save();
        break;
      case "save_profile_image":
        save_profile_image();
        break;
      case "logout":
        logout();
        break;
      case "register":
        registration();
        break;
      case "login":
        login();
        break;
      case "verify_account":
        verify_account();
        break;
      case "check_verification_status":
        check_verification_status();
        break;
      case "get_all_companies":
        get_all_companies();
        break;
      case "register_company":
        register_company();
        break;
      case "save_company_image":
        save_company_image();
        break;
      case "company_save":
        company_save();
        break;
      case "verify_company":
        verify_company();
        break;
      case "save_industry":
        save_industry();
        break;
      case "add_education":
        add_education();
        break;
      case "edit_education":
        edit_education();
        break;
      case "add_work_experience":
        add_work_experience();
        break;
      case "edit_work_experience":
        edit_work_experience();
        break;
      case "add_skills":
        add_skills();
        break;
      case "get_all_skills":
        get_all_skills();
        break;
      case "add_job":
        add_job();
        break;
      case "job_status_save":
        job_status_save();
        break;
      case "update_job":
        update_job();
        break;
      case "applicant_apply":
        applicant_apply();
        break;
      case "set_interview":
        set_interview();
        break;
      case "application_status_save":
        application_status_save();
        break;
      case "add_search_keyword":
        add_search_keyword();
        break;
      case "add_title":
        add_title();
        break;
      case "add_job_type":
        add_job_type();
        break;
      case "add_work_schedules":
        add_work_schedules();
        break;
      case "add_base_pay":
        add_base_pay();
        break;
      case "add_location_type":
        add_location_type();
        break;
      case "add_rating":
        add_rating();
        break;
      case "get_rating":
        get_rating();
        break;
      case "get_all_ratings":
        get_all_ratings();
        break;
      case "validate_otp":
        validate_otp();
        break;
      case "invitation_confirmation":
        invitation_confirmation();
        break;
      case "remove_certificate":
        remove_certificate();
        break;
      case "add_certificate":
        add_certificate();
        break;
      case "get_company_rating":
        get_company_rating();
        break;
      case "add_company_rating":
        add_company_rating();
        break;
      case "get_line_chart":
        get_line_chart();
        break;
      case "get_forecasting":
        get_forecasting();
        break;
      default:
        $response["success"] = false;
        $response["message"] = "Case action not found!";

        null;
        $helpers->return_response($response);
    }
  }
} catch (Exception $e) {
  $response["success"] = false;
  $response["message"] = $e->getMessage();
  $helpers->return_response($response);
}

function get_forecasting()
{
  global $helpers, $_POST, $conn;

  // $month = $_POST["month"];
  // $year = $_POST["year"];
  $industry_id = $_POST["industry_id"];

  $query = "SELECT 
              com.name as 'company_name',
              COUNT(com.name) as 'count',
              c.date_separated
            FROM users u 
            INNER JOIN candidates c
            ON c.user_id = u.id
            LEFT JOIN job j
            ON j.id = c.job_id
            INNER JOIN company com
            ON com.id = j.company_id
            WHERE JSON_CONTAINS(j.industries, '$industry_id', '$')
            AND u.role = 'applicant'
            AND c.status = 'Resigned'
            GROUP BY com.name
            ORDER BY COUNT(com.name) DESC, c.date_separated DESC";
  $comm = $conn->query($query);

  $table_data = null;

  while ($row = $comm->fetch_object()) {
    $table_data .= "<tr>";
    $table_data .= "<td>$row->company_name</td>";
    $table_data .= "<td> <strong>$row->count</strong> in the next few weeks/months</td>";
    $table_data .= "</tr>";
  }

  $res["table_data"] = $table_data;

  $helpers->return_response($res);
}

function get_line_chart()
{
  global $helpers, $_POST, $conn;

  $quarter = $_POST["quarter"];
  $industry_id = $_POST["industry_id"];
  $year = $_POST["year"];

  $formattedQuarter = "";
  switch ($quarter) {
    case "1":
      $formattedQuarter = "1st Quarter";
      break;
    case "2":
      $formattedQuarter = "2nd Quarter";
      break;
    case "3":
      $formattedQuarter = "3rd Quarter";
      break;
    case "4":
      $formattedQuarter = "4th Quarter";
      break;
    case "5":
      $formattedQuarter = "whole year";
      break;
  }

  $months = array(
    array("$year-01-01", "$year-02-01", "$year-03-01"),
    array("$year-04-01", "$year-05-01", "$year-06-01"),
    array("$year-07-01", "$year-08-01", "$year-09-01"),
    array("$year-10-01", "$year-11-01", "$year-12-01"),
    array(
      "$year-01-01",
      "$year-02-01",
      "$year-03-01",
      "$year-04-01",
      "$year-05-01",
      "$year-06-01",
      "$year-07-01",
      "$year-08-01",
      "$year-09-01",
      "$year-10-01",
      "$year-11-01",
      "$year-12-01"
    ),
  );

  $selectedQuarter = $months[intval($quarter) - 1];

  $startSelectedQuarter = $selectedQuarter[0];
  $endSelectedQuarter = date("Y-m-t", strtotime(end($months[intval($quarter) - 1])));

  $res = array(
    "quarter" => "--",
    "year" => "--",
    "line_data" => array(),
  );

  foreach ($selectedQuarter as $quarterDate) {
    $lastDateOfMonth = date("Y-m-t", strtotime($quarterDate));
    $month = date("M", strtotime($quarterDate));

    $query = $conn->query(
      "SELECT 
        j.title,
        j.industries,
        j.date_created
      FROM candidates c
      LEFT JOIN job j
      ON j.id = c.job_id
      WHERE c.date_applied BETWEEN '$quarterDate' AND '$lastDateOfMonth'
      AND JSON_CONTAINS(j.industries, '$industry_id', '$')
      AND j.status <> 'inactive'"
    );

    $res["line_data"][$month] = $query->num_rows;
  }

  $byYearQ = "SELECT 
                sq.title,
                SUM(sq.count) as 'count'
              FROM (
                SELECT 
                      j.title,
                      COUNT(j.title) as 'count'
                  FROM candidates c
                  LEFT JOIN job j
                  ON j.id = c.job_id
                  WHERE YEAR(c.date_applied)='$year'
                  AND JSON_CONTAINS(j.industries, '$industry_id', '$')
                  AND j.status <> 'inactive'
                  GROUP BY j.title
              )sq";

  $byYear = $conn->query($byYearQ);

  if ($byYear->num_rows > 0) {
    $byYearData = $byYear->fetch_object();
    if ($byYearData->title && $byYearData->count) {
      $res["year"] = "<strong>$byYearData->count</strong> Candidate(s) for <strong> $byYearData->title</strong> Job Postings from the Year <strong>$year</strong>";
    }
  }

  $byQuarterQ = "SELECT 
                    sq.title,
                    SUM(sq.count) as 'count'
                  FROM (
                    SELECT 
                          j.title,
                          COUNT(j.title) as 'count'
                      FROM candidates c
                      LEFT JOIN job j
                      ON j.id = c.job_id
                      WHERE c.date_applied BETWEEN '$startSelectedQuarter' AND '$endSelectedQuarter'
                      AND JSON_CONTAINS(j.industries, '$industry_id', '$')
                      AND j.status <> 'inactive'
                      GROUP BY j.title
                  )sq";

  $byQuarter = $conn->query($byQuarterQ);

  if ($byQuarter->num_rows > 0) {
    $byQuarterData = $byQuarter->fetch_object();
    if ($byQuarterData->title && $byQuarterData->count) {
      if ($quarter == 5) {
        $res["quarter"] = "<strong>$byQuarterData->count</strong> Candidate(s) for <strong> $byQuarterData->title</strong> Job Postings of <strong>$formattedQuarter</strong> <strong>$year</strong>";
      } else {
        $res["quarter"] = "<strong>$byQuarterData->count</strong> Candidate(s) for <strong> $byQuarterData->title</strong> Job Postings from <strong>$formattedQuarter</strong> of Year <strong>$year</strong>";
      }
    }
  }

  $helpers->return_response($res);
}

function add_company_rating()
{
  global $helpers, $_SESSION, $_POST, $conn;

  $rated_by = $_SESSION["id"];
  $company_id = $_POST["company_id"];
  $management = $_POST["management"];
  $work_life_balance = $_POST["work_life_balance"];
  $salary_benefits = $_POST["salary_benefits"];
  $feedback = $_POST["feedback"];

  $comm = null;
  $action = "added";

  $rateRes = $helpers->select_all_individual("company_ratings", "company_id='$company_id' AND rated_by='$rated_by'");

  if ($rateRes) {
    $comm = $conn->query("UPDATE company_ratings SET management='$management', work_life_balance='$work_life_balance', salary_benefits='$salary_benefits', feedback='$feedback' WHERE company_id ='$company_id' AND rated_by='$rated_by'");
    $action = "updated";
  } else {
    $rateData = array(
      "rated_by" => $rated_by,
      "company_id" =>  $company_id,
      "management" => $management,
      "work_life_balance" => $work_life_balance,
      "salary_benefits" => $salary_benefits,
      "feedback" => $feedback,
    );
    $comm = $helpers->insert("company_ratings", $rateData);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Ratings successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);

  $helpers->return_response($_POST);
}

function get_company_rating()
{
  global $helpers, $_SESSION, $_POST;

  $rated_by = $_SESSION["id"];
  $company_id = $_POST["company_id"];

  $rateRes = $helpers->select_all_individual("company_ratings", "company_id='$company_id' AND rated_by='$rated_by'");

  $helpers->return_response($rateRes ?  $rateRes : null);
}

function add_certificate()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $title = $_POST["title"];
  $date_acquired = $_POST["acquired"];

  $input_cert = $_FILES["input_cert"];
  $url_cert = $_POST["url_cert"];

  $path = "../uploads/certificates";
  $cert = $helpers->upload_file($input_cert, $path);

  $certData = array(
    "user_id" => $id,
    "title" => $title,
    "date_acquired" => $date_acquired,
    "cert" => $cert->success ?  SERVER_NAME . "/uploads/certificates/$cert->file_name" : $url_cert,
  );

  $cert_id = $helpers->insert("certificates", $certData);

  if ($cert_id) {
    $response["success"] = true;
    $response["message"] = "Certificate successfully uploaded.";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function remove_certificate()
{
  global $helpers, $_POST, $conn;

  $id = $_POST["id"];

  $comGetCert = $conn->query("SELECT id, `cert` FROM certificates WHERE id = '$id'");

  if ($comGetCert->num_rows) {
    $certData = $comGetCert->fetch_object();

    $deleteComm = $conn->query("DELETE FROM certificates WHERE id='$id'");

    if ($deleteComm) {
      if ($certData->cert) {
        $certFile = str_replace(SERVER_NAME, "..", $certData->cert);

        if (file_exists($certFile)) {
          unlink($certFile);
        }
      }

      $response["success"] = true;
    } else {
      $response["success"] = false;
      $response["message"] = $conn->error;
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Error removing certificate";
  }

  $helpers->return_response($response);
}

function invitation_confirmation()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["id"]);
  $action = $helpers->decrypt($_POST["action"]);

  $confirmationData = $helpers->select_all_individual("candidates", "id=$id");

  if ($confirmationData->invitation_confirmation == "yes") {
    $response["success"] = false;
    $response["message"] = "You already responded to the interview confirmation.<br>Please wait for further instructions.";
  } else {
    $updateData = array(
      "invitation_confirmation" => $action
    );

    $up = $helpers->update("candidates", $updateData, "id", $id);

    if ($up) {
      $response["success"] = true;
      if ($action == "yes") {
        $response["message"] = "Interview confirmation successfully confirm.";
      } else {
        $response["message"] = "Interview confirmation set to not available for the scheduled interview";
      }
    } else {
      $response["success"] = false;
      $response["message"] = $conn->error;
    }
  }

  $helpers->return_response($response);
}

function sendEmail($email, $body, $name, $subject)
{
  include(__DIR__ . "/ClassSendEmail.php");

  $sendSmtp = new SendEmail($email, $body, $name, $subject);

  $success = $sendSmtp->response["success"];
  $message = $sendSmtp->response["message"];
}

function validate_otp()
{
  global $helpers, $_POST, $conn;

  $otp = $_POST["otp"];
  $id = $helpers->decrypt($_POST["token"]);

  $checkOtp = $helpers->select_all_with_params("otp", "otp='$otp'");

  if (count($checkOtp) > 0) {
    $comm = $conn->query("DELETE FROM otp WHERE user_id='$id'");

    if ($comm) {
      $response["success"] = true;
      $response["message"] = "Email verified successfully.";
    } else {
      $response["success"] = false;
      $response["message"] = $conn->error;
    }
  } else {
    $response["success"] = false;
    $response["message"] = "One time password (OTP) not match";
  }

  $helpers->return_response($response);
}

function get_all_ratings()
{
  global $helpers, $_POST;

  $user_id = $_POST["user_id"];

  $rateRes = $helpers->select_all_with_params("ratings", "user_id='$user_id'");

  $helpers->return_response($rateRes);
}

function get_rating()
{
  global $helpers, $_SESSION, $_POST;

  $rated_by = $_SESSION["id"];
  $applicantId = $_POST["applicantId"];

  $rateRes = $helpers->select_all_individual("ratings", "user_id='$applicantId' AND rated_by='$rated_by'");

  $helpers->return_response($rateRes ?  $rateRes : null);
}

function add_rating()
{
  global $helpers, $_SESSION, $_POST, $conn;

  $rated_by = $_SESSION["id"];
  $applicantId = $_POST["applicantId"];
  $soft_skills = $_POST["soft_skills"];
  $communication = $_POST["communication"];
  $flexibility = $_POST["flexibility"];
  $feedback = $_POST["feedback"];

  $comm = null;
  $action = "added";

  $rateRes = $helpers->select_all_individual("ratings", "user_id='$applicantId' AND rated_by='$rated_by'");

  if ($rateRes) {
    $comm = $conn->query("UPDATE ratings SET soft_skills='$soft_skills', communication='$communication', flexibility='$flexibility', feedback='$feedback' WHERE user_id='$applicantId' AND rated_by='$rated_by'");
    $action = "updated";
  } else {
    $rateData = array(
      "user_id" =>  $applicantId,
      "rated_by" => $rated_by,
      "soft_skills" => $soft_skills,
      "communication" => $communication,
      "flexibility" => $flexibility,
      "feedback" => $feedback,
    );
    $comm = $helpers->insert("ratings", $rateData);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Ratings successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);

  $helpers->return_response($_POST);
}

function add_location_type()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $location_type = isset($_POST["location_type"]) ? $helpers->array_unique_custom($_POST["location_type"]) : null;

  $preference = array(
    "user_id" => $id,
    "location_type" => $location_type ? json_encode($location_type) : "set_null"
  );

  $comm = null;
  $action = "added";

  $preferenceData = $helpers->select_all_individual("job_preference", "user_id='$id'");

  if ($preferenceData) {
    if ($preferenceData->location_type) {
      $action = "updated";
    } else {
      $action = "added";
    }
    $comm = $helpers->update("job_preference", $preference, "user_id", $id);
  } else {
    $comm = $helpers->insert("job_preference", $preference);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Job types successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_base_pay()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $min = isset($_POST["min"]) ? doubleval(str_replace(",", "", $_POST["min"])) : null;
  $range = isset($_POST["range"]) ? $_POST["range"] : null;

  $basePay =  $min && $range ? (number_format($min, 0, "", ",") . " $range") : null;

  $preference = array(
    "user_id" => $id,
    "base_pay" => $basePay ? $basePay : "set_null"
  );

  $comm = null;
  $action = "added";

  $preferenceData = $helpers->select_all_individual("job_preference", "user_id='$id'");

  if ($preferenceData) {
    if ($preferenceData->base_pay) {
      $action = "updated";
    } else {
      $action = "added";
    }
    $comm = $helpers->update("job_preference", $preference, "user_id", $id);
  } else {
    $comm = $helpers->insert("job_preference", $preference);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Base pay successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_work_schedules()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);

  $days = isset($_POST["days"]) ? $_POST["days"] : null;
  $shifts = isset($_POST["shifts"]) ? $_POST["shifts"] : null;
  $schedules = isset($_POST["schedules"]) ? $_POST["schedules"] : null;

  $work_schedules = array();

  if ($days) $work_schedules["days"] = $days;
  if ($shifts) $work_schedules["shifts"] = $shifts;
  if ($schedules) $work_schedules["schedules"] = $schedules;

  $preference = array(
    "user_id" => $id,
    "work_schedules" => count($work_schedules) > 0 ? json_encode($work_schedules) : "set_null"
  );

  $comm = null;
  $action = "added";

  $preferenceData = $helpers->select_all_individual("job_preference", "user_id='$id'");

  if ($preferenceData) {
    if ($preferenceData->work_schedules) {
      $action = "updated";
    } else {
      $action = "added";
    }
    $comm = $helpers->update("job_preference", $preference, "user_id", $id);
  } else {
    $comm = $helpers->insert("job_preference", $preference);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Work schedules successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_job_type()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $job_type = isset($_POST["job_type"]) ? $helpers->array_unique_custom($_POST["job_type"]) : null;

  $preference = array(
    "user_id" => $id,
    "job_types" => $job_type ? json_encode($job_type) : "set_null"
  );

  $comm = null;
  $action = "added";

  $preferenceData = $helpers->select_all_individual("job_preference", "user_id='$id'");

  if ($preferenceData) {
    if ($preferenceData->job_types) {
      $action = "updated";
    } else {
      $action = "added";
    }
    $comm = $helpers->update("job_preference", $preference, "user_id", $id);
  } else {
    $comm = $helpers->insert("job_preference", $preference);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Job types successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_title()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $titles = $helpers->array_unique_custom($_POST["title"]);

  $preference = array(
    "user_id" => $id,
    "job_title" => json_encode($titles)
  );

  $comm = null;
  $action = "added";
  $preferenceData = $helpers->select_all_individual("job_preference", "user_id='$id'");

  if ($preferenceData) {
    if ($preferenceData->job_title) {
      $action = "updated";
    } else {
      $action = "added";
    }
    $comm = $helpers->update("job_preference", $preference, "user_id", $id);
  } else {
    $comm = $helpers->insert("job_preference", $preference);
    $action = "added";
  }

  if ($comm) {
    $response["success"] = true;
    $response["message"] = "Job titles successfully $action";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_search_keyword()
{
  global $helpers, $_POST, $conn;

  $keyword = $_POST["keyword"];
  $job_type = $_POST["job_type"];

  $keywordData = array(
    "keywords" => $keyword
  );

  $inData = $helpers->insert("search_keywords", $keywordData);

  $response["link"] = SERVER_NAME . "/public/views/job-listing?k=$keyword" . ($job_type ? "&&j=$job_type" : "");

  $helpers->return_response($response);
}

function application_status_save()
{
  global $helpers, $_POST, $conn;

  $candidate_id = $_POST["candidate_id"];
  $action = $_POST["action"];

  $data = array("status" => $action);

  if ($action == "Hired") {
    $data["date_hired"] = date("Y-m-d H:i:s");
  }

  if ($action == "Terminated" || $action == "Resigned") {
    $data["date_separated"] = date("Y-m-d H:i:s");
  }

  $updateJobStatus = $helpers->update("candidates", $data, "id", $candidate_id);

  if ($updateJobStatus) {
    $response["success"] = true;

    $candidateData = $helpers->select_all_individual("candidates", "id='$candidate_id'");
    $jobData = $helpers->select_all_individual("job", "id='$candidateData->job_id'");
    $companyData = $helpers->select_all_individual("company", "id='$jobData->company_id'");

    $data = array(
      "user_id" => $candidateData->user_id,
      "job_title" => $jobData->title,
      "company_id" => $jobData->company_id,
      "industry_id" => $companyData->industry_id,
    );

    if ($action == "Hired") {
      $response["message"] = "Applicant successfully hired";

      $data["work_from"] = date("F Y");
      $data["work_to"] = "Present";

      $conn->query("UPDATE candidates SET `status`='Withdrawn' WHERE user_id='$candidateData->user_id' AND `status`='Applied'");

      func_add_work_experience($data, true);
    } else if ($action == "Not selected by employer") {
      $response["message"] = "Applicant successfully set to Not Selected";
    } else if ($action == "Withdrawn") {
      $response["message"] = "Application successfully withdrawn";
    } else if ($action == "Terminated") {
      $response["message"] = "Employee successfully terminated";
    } else if ($action == "Resigned") {
      $response["message"] = "Employee successfully resigned";
    }

    if ($action == "Terminated" || $action == "Resigned") {

      $response["id"] = $candidateData->user_id;
      $response["name"] = $helpers->get_full_name($candidateData->user_id);

      $data["work_to"] = date("F Y");

      func_add_work_experience($data, true);
    }
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function func_add_work_experience($postData, $auto = false)
{
  global $helpers;

  $user_id = $postData["user_id"];
  $job_title = $postData["job_title"];
  $company_id = $postData["company_id"];
  $industry_id = $postData["industry_id"];
  $work_from = isset($postData["work_from"]) ? $postData["work_from"] : null;
  $work_to = $postData["work_to"];

  $workExp = $helpers->select_all_individual("work_experience", "user_id='$user_id' AND company_id='$company_id'");

  $data = array(
    "user_id" => $user_id,
    "job_title" => $job_title,
    "company_id" => $company_id,
    "industry_id" => $industry_id,
    "work_from" => $work_from,
    "work_to" => $work_to,
    "is_automatic" => $auto ? "1" : "set_zero"
  );

  if ($workExp) {
    $helpers->update("work_experience", $data, "id", $workExp->id);
  } else {
    $helpers->insert("work_experience", $data);
  }
}

function set_interview()
{
  global $helpers, $_POST, $conn;

  $candidate_id = $_POST["candidate_id"];

  $applicant_data = $helpers->get_user_by_id($_POST["applicant_id"]);
  $job_data = $helpers->select_all_individual("job", "id='$_POST[job_id]'");
  $company_data = $helpers->select_all_individual("company", "id='$job_data->company_id'");

  $job_title = $job_data->title;
  $company_name = $company_data->name;

  $interview_date = $_POST["interview_date"];
  $setup = $_POST["setup"];
  $time_from = $_POST["time_from"];
  $time_to = $_POST["time_to"];


  $subject = "";
  $html_body = "";

  if ($setup == "On site") {
    $location = "$company_data->address $company_data->district";
    $html_body = file_get_contents("./onsite-interview-email-template.html");
    $html_body = str_replace('%location%', $location, $html_body);

    $subject = "On-Site Interview Invitation for $job_title Position at $company_name";
  } else {
    $html_body = file_get_contents("./online-interview-email-template.html");

    $subject = "Invitation to Interview for $job_title at $company_name";
  }

  if (!empty($html_body)) {
    $html_body = str_replace('%name%', $helpers->get_full_name($applicant_data->id), $html_body);
    $html_body = str_replace('%job_title%', $job_title, $html_body);
    $html_body = str_replace('%company_name%', $company_name, $html_body);
    $html_body = str_replace('%date%', date("F d, Y", strtotime($interview_date)), $html_body);
    $html_body = str_replace('%time%', date("h:i A", strtotime($time_from)) . " - " . date("h:i A", strtotime($time_to)), $html_body);

    $encrypted_id = $helpers->encrypt($candidate_id);
    $encrypted_yes = $helpers->encrypt("yes");
    $encrypted_no = $helpers->encrypt("no");

    $html_body = str_replace('%yes_action%', (SERVER_NAME . "/public/accept_invitation?i=$encrypted_id&a=$encrypted_yes"), $html_body);
    $html_body = str_replace('%no_action%', (SERVER_NAME . "/public/accept_invitation?i=$encrypted_id&a=$encrypted_no"), $html_body);

    sendEmail($applicant_data->email, $html_body, $helpers->get_full_name($applicant_data->id), $subject);
  }

  $candidateData = array(
    "status" => "Interviewing",
    "setup" => $setup,
    "interview_date" => "$interview_date",
    "interview_time" => "$time_from - $time_to"
  );

  $candidate_id = $helpers->update("candidates", $candidateData, "id", $candidate_id);

  if ($candidate_id) {
    $response["success"] = true;
    $response["message"] = "Interview date set successfully";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function applicant_apply()
{
  global $helpers, $_POST, $conn;

  $job_id = $_POST["job_id"];
  $user_id = $_POST["user_id"];

  $candidateData = array(
    "user_id" => $user_id,
    "job_id" => $job_id,
    "status" => "Applied"
  );

  $candidate_id = $helpers->insert("candidates", $candidateData);

  if ($candidate_id) {
    $response["success"] = true;
    $response["message"] = "Applied Successfully";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function update_job()
{
  global $helpers, $_POST, $conn;

  $job_id = $_POST["job_id"];

  $title = $_POST["title"];
  $job_type = $_POST["job_type"];
  $experience_level = $_POST["experience_level"];
  $location_type = $_POST["location_type"];
  $schedule = $_POST["schedule"];

  $min = doubleval(str_replace(",", "", $_POST["min"]));
  $max = doubleval(str_replace(",", "", $_POST["max"]));
  $range = $_POST["range"];
  $benefits = $_POST["benefits"];

  $post_qualifications = $_POST["qualifications"];
  $post_experience = isset($_POST["experience"]) ? $_POST["experience"] : null;

  $pay = (number_format($min, 2, ".", ",") . " - " . number_format($max, 2, ".", ",") . " $range");

  $description = $_POST["description"];

  for ($i = 0; $i < count($post_qualifications); $i++) {
    $qualification = $post_qualifications[$i];

    if (!is_numeric($qualification)) {
      $skillData = $helpers->select_all_individual("skills_list", "LOWER(name)=LOWER('$qualification')");

      if (!$skillData) {
        $skill_id = $helpers->insert("skills_list", array("name" => ucwords($qualification)));

        $post_qualifications[$i] = $skill_id;
      }
    } else {
      $post_qualifications[$i] = intval($qualification);
    }
  }

  if ($post_experience) {
    for ($i = 0; $i < count($post_experience); $i++) {
      $experience = $post_experience[$i];

      if (!is_numeric($experience)) {
        $experienceData = $helpers->select_all_individual("experience_list", "LOWER(name)=LOWER('$experience')");

        if (!$experienceData) {
          $experience_id = $helpers->insert("experience_list", array("name" => ucwords($experience)));

          $post_experience[$i] = $experience_id;
        } else {
          $post_experience[$i] = intval($experienceData->id);
        }
      } else {
        $post_experience[$i] = intval($experience);
      }
    }
  }

  $jobPostData = array(
    "title" => ucwords($title),
    "type" => $job_type,
    "experience_level" => $experience_level,
    "location_type" => $location_type,
    "schedule" => json_encode($schedule),
    "description" => nl2br($description),
    "pay" => $pay,
    "benefits" => json_encode($benefits),
    "qualifications" => json_encode($post_qualifications),
    "experience" => json_encode($post_experience),
    "status" => "active"
  );

  $job_id = $helpers->update("job", $jobPostData, "id", $job_id);

  if ($job_id) {
    $response["success"] = true;
    $response["message"] = "Job Updated Successfully";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function job_status_save()
{
  global $helpers, $_POST, $conn;

  $job_id = $_POST["job_id"];
  $action = $_POST["action"];

  $updateJobStatus = $helpers->update("job", array("status" => $action), "id", $job_id);

  if ($updateJobStatus) {
    $response["success"] = true;
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_job()
{
  global $helpers, $_POST, $conn;

  $company_id = $_POST["company_id"];

  $title = $_POST["title"];
  $job_type = $_POST["job_type"];
  $experience_level = $_POST["experience_level"];
  $location_type = $_POST["location_type"];
  $schedule = $_POST["schedule"];

  $industries = $_POST["industry"];

  $min = doubleval(str_replace(",", "", $_POST["min"]));
  $max = doubleval(str_replace(",", "", $_POST["max"]));
  $range = $_POST["range"];
  $benefits = $_POST["benefits"];

  $post_qualifications = $_POST["qualifications"];
  $post_experience = isset($_POST["experience"]) ? $_POST["experience"] : null;

  $pay = (number_format($min, 2, ".", ",") . " - " . number_format($max, 2, ".", ",") . " $range");

  $description = $_POST["description"];

  for ($i = 0; $i < count($post_qualifications); $i++) {
    $qualification = $post_qualifications[$i];

    if (!is_numeric($qualification)) {
      $skillData = $helpers->select_all_individual("skills_list", "LOWER(name)=LOWER('$qualification')");

      if (!$skillData) {
        $skill_id = $helpers->insert("skills_list", array("name" => ucwords($qualification)));

        $post_qualifications[$i] = $skill_id;
      }
    } else {
      $post_qualifications[$i] = intval($qualification);
    }
  }

  for ($i = 0; $i < count($industries); $i++) {
    $industry = $industries[$i];

    if (!is_numeric($qualification)) {
      $industryData = $helpers->select_all_individual("industries", "LOWER(name)=LOWER('$industry')");

      if (!$industryData) {
        $industry_id = $helpers->insert("industries", array("name" => ucwords($industry)));

        $post_qualifications[$i] = $industry_id;
      }
    } else {
      $industries[$i] = intval($industry);
    }
  }

  if ($post_experience) {
    for ($i = 0; $i < count($post_experience); $i++) {
      $experience = $post_experience[$i];

      if (!is_numeric($experience)) {
        $experienceData = $helpers->select_all_individual("experience_list", "LOWER(name)=LOWER('$experience')");

        if (!$experienceData) {
          $experience_id = $helpers->insert("experience_list", array("name" => ucwords($experience)));

          $post_experience[$i] = $experience_id;
        } else {
          $post_experience[$i] = intval($experienceData->id);
        }
      } else {
        $post_experience[$i] = intval($experience);
      }
    }
  }

  $jobPostData = array(
    "company_id" => $company_id,
    "title" => ucwords($title),
    "type" => $job_type,
    "experience_level" => $experience_level,
    "location_type" => $location_type,
    "schedule" => json_encode($schedule),
    "description" => nl2br($description),
    "pay" => $pay,
    "benefits" => json_encode($benefits),
    "qualifications" => json_encode($post_qualifications),
    "qualifications" => json_encode($industries),
    "experience" => json_encode($post_experience),
    "status" => "active"
  );

  $job_id = $helpers->insert("job", $jobPostData);

  if ($job_id) {
    $response["success"] = true;
    $response["message"] = "Job Posted Successfully";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function get_all_skills()
{
  global $helpers, $_GET;

  $params = isset($_GET["s"]) ? "LOWER(name) LIKE LOWER('%$_GET[s]%') " : "";

  $skills_list = $helpers->select_all_with_params("skills_list", "$params ");

  $data = array();

  if (count($skills_list) > 0) {
    foreach ($skills_list as $skill) {
      array_push(
        $data,
        array(
          "id" => $skill->id,
          "name" => $skill->name,
        )
      );
    }
  }

  $helpers->return_response($data);
}

function add_skills()
{
  global $helpers, $_POST, $conn;

  $skill_id = isset($_POST["skill_id"]) ? $_POST["skill_id"] : null;
  $token = $_POST["token"];
  $skill = isset($_POST["skill_name"]) ? $_POST["skill_name"] : null;

  if (!$skill_id) {
    $skillData = $helpers->select_all_individual("skills_list", "LOWER(name)=LOWER('$skill')");

    if (!$skillData) {
      $skill_id = $helpers->insert("skills_list", array("name" => ucwords($skill)));
    } else {
      $skill_id = $skillData->id;
    }
  }

  $applicantSkillData = array(
    "user_id" => $helpers->decrypt($token),
    "skill_id" => $skill_id
  );

  $applicationId = $helpers->insert("applicant_skills", $applicantSkillData);

  if ($applicationId) {
    $response["success"] = true;
    $response["message"] = "Skill successfully added";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function edit_work_experience()
{
  global $helpers, $_POST, $conn;

  $user_id = $helpers->decrypt($_POST["token"]);
  $company_id = $_POST["company_id"];
  $industry_id = $_POST["industry_id"];
  $work_experience_id = $_POST["work_experience_id"];

  $job_title = $_POST["job_title"];

  $company_name = $_POST["company_name"];

  $currently_working = isset($_POST["currently_working"]) ? $_POST["currently_working"] : null;

  $work_from_month = $_POST["work_from_month"];
  $work_from_year = $_POST["work_from_year"];

  $work_to_month = isset($_POST["work_to_month"]) ? $_POST["work_to_month"] : null;
  $work_to_year = isset($_POST["work_to_year"]) ? $_POST["work_to_year"] : null;

  $work_from = "$work_from_month $work_from_year";
  $work_to = $currently_working ? $currently_working : "$work_to_month $work_to_year";

  if (empty($company_id)) {
    $companyData = array(
      "name" => $company_name
    );

    $company_id = $helpers->insert("company", $companyData);
  }

  $workExpData = array(
    "user_id" => $user_id,
    "job_title" => ucwords($job_title),
    "company_id" => $company_id,
    "industry_id" => $industry_id,
    "work_from" => $work_from,
    "work_to" => $work_to
  );

  $updateWorkExp = $helpers->update("work_experience", $workExpData, "id", $work_experience_id);

  if ($updateWorkExp) {
    $response["success"] = true;
    $response["message"] = "Work Experience successfully updated";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_work_experience()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);

  $job_title = $_POST["job_title"];

  $company_id = $_POST["company_id"];
  $industry_id = $_POST["industry_id"];
  $company_name = $_POST["company_name"];

  $currently_working = isset($_POST["currently_working"]) ? $_POST["currently_working"] : null;

  $work_from_month = $_POST["work_from_month"];
  $work_from_year = $_POST["work_from_year"];

  $work_to_month = isset($_POST["work_to_month"]) ? $_POST["work_to_month"] : null;
  $work_to_year = isset($_POST["work_to_year"]) ? $_POST["work_to_year"] : null;

  $work_from = "$work_from_month $work_from_year";
  $work_to = $currently_working ? $currently_working : "$work_to_month $work_to_year";

  if (empty($company_id)) {
    $companyData = array(
      "name" => $company_name
    );

    $company_id = $helpers->insert("company", $companyData);
  }

  $workExpData = array(
    "user_id" => $id,
    "job_title" => ucwords($job_title),
    "company_id" => $company_id,
    "industry_id" => $industry_id,
    "work_from" => $work_from,
    "work_to" => $work_to
  );

  $workExpID = $helpers->insert("work_experience", $workExpData);

  if ($workExpID) {
    $response["success"] = true;
    $response["message"] = "Work Experience successfully added";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function edit_education()
{
  global $helpers, $_POST, $conn;

  $id = $_POST['id'];

  $user_id = $helpers->decrypt($_POST["token"]);
  $attainment = $_POST["attainment"];
  $course = empty($_POST["course"]) ? "set_null" : ucwords($_POST["course"]);
  $school_name = ucwords($_POST["school_name"]);
  $school_year = $_POST["school_year"];

  $educationData = array(
    "user_id" => $user_id,
    "attainment_id" => $attainment,
    "course" => $course,
    "school_name" => $school_name,
    "sy" => $school_year
  );

  $education_id = $helpers->update("education", $educationData, "id", $id);

  if ($education_id) {
    $response["success"] = true;
    $response["message"] = "Education added updated";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_education()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $attainment = $_POST["attainment"];
  $course = empty($_POST["course"]) ? "set_null" : ucwords($conn->escape_string($_POST["course"]));
  $school_name = ucwords($conn->escape_string($_POST["school_name"]));
  $school_year = $_POST["school_year"];

  $educationData = array(
    "user_id" => $id,
    "attainment_id" => $attainment,
    "course" => $course,
    "school_name" => $school_name,
    "sy" => $school_year
  );

  $education_id = $helpers->insert("education", $educationData);

  if ($education_id) {
    $response["success"] = true;
    $response["message"] = "Education added successfully";

    if ($_POST["course"] || $_POST["school_name"]) {
      if ($_POST["school_name"]) {
        $schoolQ = $conn->query("SELECT * FROM schools WHERE LOWER(`name`)=LOWER('$school_name')");

        if ($schoolQ->num_rows == 0) {
          $helpers->insert("schools", array("name" => $school_name));
        }
      }

      if ($_POST["course"]) {
        $courseQ = $conn->query("SELECT * FROM courses WHERE LOWER(`title`)=LOWER('$course')");

        if ($courseQ->num_rows == 0) {
          $helpers->insert("courses", array("title" => $course));
        }
      }
    }
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function save_industry()
{
  global $helpers, $_POST, $conn;

  $action = $_POST["action"];
  $name = $_POST["name"];
  $params = isset($_POST["id"]) ? " AND id <> '$_POST[id]'" : "";

  $lowerName = strtolower($name);
  $getIndustry = $helpers->select_all_with_params("industries", "LOWER(name) = '$lowerName' $params");

  if (count($getIndustry) == 0) {
    $comm = null;
    $message = "";

    if ($action == "insert") {
      $insertData = array(
        "name" => ucwords($name)
      );

      $comm = $helpers->insert("industries", $insertData);
      $message = "Industry successfully added";
    } else if ($action == "update") {
      $updateData = array(
        "name" => ucwords($name)
      );

      $comm = $helpers->update("industries", $updateData, "id", $_POST['id']);
      $message = "Industry successfully updated";
    } else {
      $response["success"] = false;
      $response["message"] = "Action not found";
    }

    if ($comm) {
      $response["success"] = true;
      $response["message"] = $message;
    } else {
      $response["success"] = false;
      $response["message"] = $conn->error;
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Industry <strong>$name</strong> already exist.";
  }

  $helpers->return_response($response);
}

function verify_company()
{
  global $helpers, $_POST, $conn;

  $id = $_POST["id"];
  $verification = $_POST["action"];
  $message = $_POST["msg"];

  $updateData = array(
    "status" => $verification,
    "message" => nl2br($message)
  );

  $update = $helpers->update("verification", $updateData, "id", $id);

  if ($update) {
    $response["success"] = true;
    $response["message"] = "Company verification successfully updated";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function company_save()
{
  global $helpers, $_POST, $conn;

  $company_id = $_POST["company_id"];
  $name = $_POST["name"];
  $contact = $_POST["contact"];
  $email = $_POST["email"];
  $companyAddress = $_POST["companyAddress"];
  $companyDistrict = $_POST["companyDistrict"];
  $industry_id = $_POST["industry"];
  $description = $_POST["description"];
  $input_business_permit = $_FILES["input_business_permit"];
  $url_business_permit = $_POST["url_business_permit"];

  $mapFrame = nl2br($_POST["mapFrame"]);

  $updateData = array();
  $update = false;

  if (empty($input_business_permit["name"]) && empty($url_business_permit)) {

    $updateData = array(
      "name" => $name,
      "contact" => $contact,
      "email" => $email,
      "address" => $companyAddress,
      "district" => $companyDistrict,
      "description" => nl2br($description),
      "industry_id" => $industry_id,
      "map_frame" => $mapFrame
    );

    $update = $helpers->update("company", $updateData, "id", $company_id);
  } else {
    $companyData = $helpers->select_all_individual("company", "id='$company_id'");

    if ($companyData) {

      if (!$companyData->verification_id) {
        $path = "../uploads/company";
        $business_permit = $helpers->upload_file($input_business_permit, $path);

        $verificationData = array(
          "business_permit" => $business_permit->success ?  SERVER_NAME . "/uploads/company/$business_permit->file_name" : $url_business_permit,
          "status" => "pending",
          "message" => "Waiting for admin to validate the business permit."
        );

        $verification_id = $helpers->insert("verification", $verificationData);

        $updateData = array(
          "verification_id" => $verification_id,
          "name" => $name,
          "contact" => $contact,
          "email" => $email,
          "address" => $companyAddress,
          "district" => $companyDistrict,
          "description" => nl2br($description),
          "industry_id" => $industry_id,
          "map_frame" => $mapFrame
        );

        $update = $helpers->update("company", $updateData, "id", $company_id);
      } else {
        $path = "../uploads/company";
        $business_permit = $helpers->upload_file($input_business_permit, $path);

        $updateVerificationData = array(
          "business_permit" => $business_permit->success ?  SERVER_NAME . "/uploads/company/$business_permit->file_name" : $url_business_permit,
          "status" => "pending",
          "message" => "Waiting for admin to validate the business permit."
        );

        $helpers->update("verification", $updateVerificationData, "id", $companyData->verification_id);
      }

      $update = true;
    } else {
      $response["success"] = false;
      $response["message"] = "Error updating company details.<br>Please try again later";
    }
  }

  if ($update) {
    $response["success"] = true;
    $response["message"] = "Company Profile updated successfully";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }


  $helpers->return_response($response);
}

function save_company_image()
{
  global $helpers, $_FILES, $_POST;

  $action = $_POST["action"];
  $set_image_null = boolval($_POST["set_image_null"]);
  $id = $_POST["id"];

  $image_url = "";

  if ($action == "upload") {
    $file = $helpers->upload_file($_FILES["file"], "../uploads/company");

    if ($file->success) {
      $file_name = $file->file_name;

      $image_url = SERVER_NAME . "/uploads/company/$file_name";

      $uploadData = array(
        "company_logo" => $file_name
      );
    } else {
      $response["success"] = false;
      $response["message"] = "Error uploading image";
    }
  } else if ($action == "reset") {
    $image_url = SERVER_NAME . "/custom-assets/images/office.png";

    $uploadData = array(
      "company_logo" => $set_image_null ? "set_null" : null,
    );
  } else {
    $image_url = SERVER_NAME . "/custom-assets/images/office.png";

    $response["success"] = false;
    $response["message"] = "Error updating image";
  }

  $update = $helpers->update("company", $uploadData, "id", $id);

  if ($update) {
    $response["success"] = true;
    $response["image_url"] = $image_url;
  } else {
    $response["success"] = false;
    $response["message"] = "Error updating image";
  }

  $helpers->return_response($response);
}

function register_company()
{
  global $helpers, $_POST, $conn;

  $id = $helpers->decrypt($_POST["token"]);
  $input_company_id = $_POST["company_id"];

  $company_name = $_POST["company_name"];
  $contact = $_POST["contact"];
  $email = $_POST["email"];
  $district = $_POST["district"];
  $address = $_POST["address"];
  $industry = $_POST["industry"];
  $description = nl2br($_POST["description"]);

  $input_company_logo = $_FILES["input_company_logo"];
  $url_company_logo = $_POST["url_company_logo"];

  $input_business_permit = $_FILES["input_business_permit"];
  $url_business_permit = $_POST["url_business_permit"];

  $mapFrame = nl2br($_POST["mapFrame"]);

  $company_id = null;

  if (!empty($input_company_id)) {
    $company_id = $input_company_id;
  } else {
    $path = "../uploads/company";

    $company_logo = $helpers->upload_file($input_company_logo, $path);
    $business_permit = $helpers->upload_file($input_business_permit, $path);

    $verificationData = array(
      "business_permit" => $business_permit->success ? SERVER_NAME . "/uploads/company/$business_permit->file_name" : $url_business_permit,
      "status" => "pending",
      "message" => "Waiting for admin to validate the business permit."
    );

    $verification_id = $helpers->insert("verification", $verificationData);

    if ($verification_id) {

      $companyData = array(
        "verification_id" => $verification_id,
        "industry_id" => $industry,
        "name" => $company_name,
        "contact" => $contact,
        "email" => $email,
        "company_logo" => $company_logo->success ? $company_logo->file_name : $url_company_logo,
        "address" => $address,
        "district" => $district,
        "description" => $description,
        "map_frame" => $mapFrame
      );

      $insertCompany = $helpers->insert("company", $companyData);

      if ($insertCompany) {
        $company_id = $insertCompany;
      } else {
        $response["success"] = false;
        $response["message"] = "Adding Company Error<br>Please try again later";

        $helpers->return_response($response);
      }
    } else {
      $response["success"] = false;
      $response["message"] = "Verification Error<br>Please try again later";

      $helpers->return_response($response);
    }
  }

  $updateData = array(
    "company_id" => $company_id
  );

  $updateData = $helpers->update("users", $updateData, "id", $id);

  if ($updateData) {
    $response["success"] = true;
    $response["message"] = "Company added successfully";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function get_all_companies()
{
  global $helpers, $_GET;

  $params = isset($_GET["s"]) ? "name LIKE '%$_GET[s]%' " : "";

  $companies = $helpers->select_all_with_params("company", "$params ");

  $data = array();

  if (count($companies) > 0) {
    foreach ($companies as $company) {
      array_push(
        $data,
        array(
          "address" => $company->district,
          "company_logo" => $helpers->get_company_logo_link($company->id),
          "description" => $company->description,
          "id" => $company->id,
          "industry_id" => $company->industry_id,
          "name" => $company->name
        )
      );
    }
  }

  $helpers->return_response($data);
}

function check_verification_status()
{
  global $helpers, $_GET;

  $verification_data = $helpers->select_all_with_params("verification", "id=$_GET[id]");

  $data = null;
  if (count($verification_data) > 0) {
    $data = $verification_data[0];
  }

  $helpers->return_response($data);
}
function verify_account()
{
  global $helpers, $_POST, $_FILES, $conn;

  $token = $_POST["token"];
  $selfie_input = $_FILES["selfie_input"];
  $selfie_url = $_POST["selfie_url"];
  $valid_id_input = $_FILES["valid_id_input"];
  $valid_id_url = $_POST["valid_id_url"];

  $path = "../uploads/verification";
  $selfie_image = $helpers->upload_file($selfie_input, $path);
  $valid_id_image = $helpers->upload_file($valid_id_input, $path);

  $user_id = $helpers->decrypt($token);

  $verificationData = array(
    "selfie" => $selfie_image->success ? $selfie_image->file_name : $selfie_url,
    "valid_id" => $valid_id_image->success ? $valid_id_image->file_name : $valid_id_url,
    "status" => "pending",
    "message" => "Waiting for admin to approved your account."
  );

  $insertVerification = $helpers->insert("verification", $verificationData);

  if ($insertVerification) {

    $updateUser = $helpers->update("users", array("verification_id" => $insertVerification), "id", $user_id);

    if ($updateUser) {
      $response["success"] = true;
      $response["message"] = "Selfie and Valid ID successfully uploaded<br>Please wait for admin approval.";
    } else {
      $response["success"] = false;
      $response["message"] = $conn->error;
    }
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function add_admin()
{
  global $helpers, $_POST, $conn;

  $fname = $_POST["fname"];
  $mname = $_POST["mname"];
  $lname = $_POST["lname"];
  $contact = $_POST["contact"];
  $email = $_POST["email"];
  $address = $_POST["address"];

  $user = $helpers->get_user_by_email($email);

  if (!$user) {
    $userData = array(
      "fname" => $fname,
      "mname" => $mname,
      "lname" => $lname,
      "address" => $address,
      "contact" => $contact,
      "email" => $email,
      "password" => password_hash($email, PASSWORD_ARGON2I),
      "role" => "admin",
      "is_password_changed" => "set_zero",
    );

    $insert = $helpers->insert("users", $userData);

    if ($insert) {
      $response["success"] = true;
      $response["message"] = "Successfully added new admin<br>The default password is the email <strong>$email</strong>";
    } else {
      $response["success"] = false;
      $response["message"] = ("Error adding new admin: " . $conn->error);
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Email already exist!";
  }

  $helpers->return_response($response);
}

function change_password()
{
  global $helpers, $_POST, $conn;

  $user = $helpers->get_user_by_id($_POST["id"]);

  if ($user) {
    if (password_verify($_POST["current_password"], $user->password)) {
      $updateData = array(
        "password" => password_hash($_POST["new_password"], PASSWORD_ARGON2I),
        "is_password_changed" => "1",
      );

      $update = $helpers->update("users", $updateData, "id", $_POST["id"]);

      if ($update) {
        $response["success"] = true;
        $response["message"] = "Password successfully updated!";
      } else {
        $response["success"] = false;
        $response["message"] = ("Error updating password: " . $conn->error);
      }
    } else {
      $response["success"] = false;
      $response["message"] = "Current password not match!";
    }
  } else {
    $response["success"] = false;
    $response["message"] = "User not found!";
  }

  $helpers->return_response($response);
}
function delete_data()
{
  global $helpers, $_POST, $conn;

  $delete = $helpers->delete($_POST["table"], $_POST["column"], $_POST["val"]);

  if ($delete) {
    $response["success"] = true;
    $response["message"] = "Item successfully deleted";
  } else {
    $response["success"] = false;
    $response["message"] = $conn->error;
  }

  $helpers->return_response($response);
}

function delete_profile()
{
  global $helpers, $_POST, $conn;

  $delete = $helpers->delete($_POST["table"], $_POST["column"], $_POST["val"]);

  if ($delete) {
    if ($_POST["table"] == "users") {
      session_unset();
      session_destroy();
    }
    $response["success"] = true;
    $response["message"] = "Profile successfully deactivated!";
  } else {
    $response["success"] = false;
    $response["message"] = ("Error deactivating account: " . $conn->error);
  }

  $helpers->return_response($response);
}

function profile_save()
{
  global $helpers, $_POST, $conn;

  $id = $_POST["id"];
  $fname = $_POST["fname"];
  $mname = $_POST["mname"];
  $lname = $_POST["lname"];
  $contact = $_POST["contact"];
  $email = $_POST["email"];
  $address = $_POST["address"];
  $district = $_POST["district"];
  $position = isset($_POST["position"]) ? $_POST["position"] : null;

  $user = $helpers->get_user_by_email($email, $id);

  if (!$user) {
    $updateData = array(
      "fname" => $fname,
      "mname" => $mname,
      "lname" => $lname,
      "address" => $address,
      "district" => $district,
      "contact" => $contact,
      "email" => $email,
      "position" => $position
    );

    $update = $helpers->update("users", $updateData, "id", $id);

    if ($update) {
      $response["success"] = true;
      $response["message"] = "Profile successfully updated!";
    } else {
      $response["success"] = false;
      $response["message"] = ("Error updating data: " . $conn->error);
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Email already registered!";
  }

  $helpers->return_response($response);
}

function save_profile_image()
{
  global $helpers, $_FILES, $_POST;

  $action = $_POST["action"];
  $set_image_null = boolval($_POST["set_image_null"]);
  $id = $_POST["id"];

  $image_url = "";

  if ($action == "upload") {
    $file = $helpers->upload_file($_FILES["file"], "../uploads/avatars");

    if ($file->success) {
      $file_name = $file->file_name;

      $image_url = SERVER_NAME . "/uploads/avatars/$file_name";

      $uploadData = array(
        "avatar" => $file_name
      );
    } else {
      $response["success"] = false;
      $response["message"] = "Error uploading image";
    }
  } else if ($action == "reset") {
    $image_url = SERVER_NAME . "/custom-assets/images/default.png";

    $uploadData = array(
      "avatar" => $set_image_null ? "set_null" : null,
    );
  } else {
    $image_url = SERVER_NAME . "/custom-assets/images/default.png";

    $response["success"] = false;
    $response["message"] = "Error updating image";
  }

  $update = $helpers->update("users", $uploadData, "id", $id);

  if ($update) {
    $response["success"] = true;
    $response["image_url"] = $image_url;
  } else {
    $response["success"] = false;
    $response["message"] = "Error updating image";
  }

  $helpers->return_response($response);
}

function logout()
{
  global $helpers;
  $user = $helpers->get_current_user();

  $path = "../views/sign-in";

  if ($user->role == "applicant") {
    $path = "../public/views/home";
  }

  $helpers->user_logout($path);
}

function registration()
{
  global $_POST, $helpers, $conn;

  $user = $helpers->get_user_by_email($_POST["email"]);

  if (!$user) {
    $registerData = array(
      "fname" => $_POST["fname"],
      "mname" => empty($_POST["mname"]) ? "set_null" : $_POST["mname"],
      "lname" => $_POST["lname"],
      "district" => $_POST["district"],
      "address" => $_POST["address"],
      "contact" => $_POST["contact"],
      "email" => $_POST["email"],
      "password" => password_hash($_POST["password"], PASSWORD_ARGON2I),
      "role" => $_POST['role']
    );

    $comm = $helpers->insert("users", $registerData);

    if ($comm) {
      $response["success"] = true;
      $response["message"] = "You are successfully registered!";
      $response["role"] = $_POST['role'];
      $response["token"] = $helpers->encrypt($comm);

      if ($_POST['role'] == "applicant") {
        $otp = $helpers->generateNumericOTP(6);
        addOtp($comm, $otp);
        $fullName = $helpers->get_full_name($comm);

        $html_body = file_get_contents("./otp-email-template.html");
        $html_body = str_replace('%name%', $fullName, $html_body);
        $html_body = str_replace('%otp%', $otp, $html_body);

        sendEmail($_POST["email"], $html_body, $fullName, "Email Verification");
      }

      $_SESSION["id"] = $comm;
    } else {
      $response["success"] = false;
      $response["message"] = $conn->error;
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Email already registered<br>Please try again.";
  }

  $helpers->return_response($response);
}

function addOtp($user_id, $otp)
{
  global $helpers;

  $checkOtp = $helpers->select_all_individual("otp", "otp='$otp'");

  if ($checkOtp) {
    addOtp($user_id, $helpers->generateNumericOTP(6));
  } else {
    $helpers->insert("otp", array("user_id" => $user_id, "otp" => "$otp"));
  }
}

function login()
{
  global $_POST, $helpers;

  $email = $_POST["email"];
  $password = $_POST["password"];

  $user = $helpers->get_user_by_email($email);

  if ($user) {
    if (password_verify($password, $user->password)) {

      if ($user->role == "employer" && !$user->company_id) {
        $response["token"] = $helpers->encrypt($user->id);
      } else if ($user->role == "applicant") {

        $getOtp = $helpers->select_all_with_params("otp", "user_id='$user->id'");

        if (count($getOtp) > 0) {
          $otp = $helpers->generateNumericOTP(6);
          addOtp($user->id, $otp);
          $fullName = $helpers->get_full_name($user->id);

          $html_body = file_get_contents("./otp-email-template.html");
          $html_body = str_replace('%name%', $fullName, $html_body);
          $html_body = str_replace('%otp%', $otp, $html_body);

          sendEmail($_POST["email"], $html_body, $fullName, "Email Verification");

          $response["location"] = (SERVER_NAME . "/views/otp?t=" . $helpers->encrypt($user->id));
        } else {
          $education = $helpers->select_all_with_params("education", "user_id='$user->id'");

          if (count($education) == 0) {
            $response["location"] = (SERVER_NAME . "/views/add-education?t=" . $helpers->encrypt($user->id));
          } else {
            $response["location"] = (SERVER_NAME . "/public/views/home");
          }
        }
      } else {
        $response["is_password_change"] = $user->is_password_changed == "0" ? false : true;
      }
      $response["success"] = true;
      $response["role"] = $user->role;

      $_SESSION["id"] = $user->id;
    } else {
      $response["success"] = false;
      $response["message"] = "Password not match.";
    }
  } else {
    $response["success"] = false;
    $response["message"] = "User not found.";
  }

  $helpers->return_response($response);
}
