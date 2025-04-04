<?php include("../backend/nodes.php"); ?>
<?php include("../components/function_components.php"); ?>
<?php
if (!isset($_SESSION["id"])) {
  header("location: ../sign-in");
}

$LOGIN_USER = $helpers->get_user_by_id($_SESSION["id"]);
$pageName = "Employees List";
?>
<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-template="vertical-menu-template-free">

<?= head($pageName) ?>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <?php include("../components/sidebar.php") ?>

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <?php include("../components/navbar.php") ?>
        <!-- / Navbar -->

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
              <div class="col-6">
                <h4 class="fw-bold py-3 mb-4">
                  <span class="text-muted fw-light"><?= $pageName ?></span>
                </h4>
              </div>
            </div>

            <div class="card">
              <div class="card-body">

                <table id="hired-table" class="table table-striped nowrap">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Title</th>
                      <th>Job Type</th>
                      <th class="text-start">Date Applied</th>
                      <th class="text-start">Hired Date</th>
                      <th class="text-start">Separation Date</th>
                      <th>Employment Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $jobs = $helpers->select_all_with_params("job", "company_id='$LOGIN_USER->company_id'");
                    if (count($jobs) > 0) :
                      foreach ($jobs as $job) :
                        $applicants = $helpers->select_all_with_params("candidates", "job_id='$job->id' AND status IN('Hired', 'Terminated', 'Resigned')");

                        if (count($applicants) == 0) continue;

                        foreach ($applicants as $applicant) :
                          $btnDropDownId = "btn-dropdown-$job->id";
                          $post_interview_time = explode(" - ", $applicant->interview_time);

                          $time_from = date("h:i A", strtotime($post_interview_time[0]));
                          $time_to = date("h:i A", strtotime($post_interview_time[1]));
                    ?>
                          <tr>
                            <td><?= $helpers->get_full_name($applicant->user_id); ?></td>
                            <td><?= $job->title ?></td>
                            <td><?= $job->type ?></td>
                            <td class="text-start"><?= date("Y-m-d", strtotime($applicant->date_applied)) ?></td>
                            <td class="text-start"><?= date("Y-m-d", strtotime($applicant->date_hired)) ?></td>
                            <td class="text-start"><?= $applicant->date_separated ?  date("Y-m-d", strtotime($applicant->date_separated)) : "<em>----</em>" ?></td>
                            <td>
                              <?php
                              $statusColor = "success";
                              if ($applicant->status == "Terminated") {
                                $statusColor = "warning";
                              } else if ($applicant->status == "Resigned") {
                                $statusColor = "danger";
                              }
                              ?>
                              <span class="badge bg-label-<?= $statusColor ?> me-1"><?= $applicant->status ?></span>
                            </td>
                            <td>
                              <div class="dropdown">

                                <button class="btn btn-primary btn-sm" type="button" id="<?= $btnDropDownId ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 More
                                </button>

                                <div class="dropdown-menu" aria-labelledby="<?= $btnDropDownId ?>" data-bs-popper="none">
                                  <button type="button" class="dropdown-item" onclick='handleOpenModal(`<?= SERVER_NAME . "/public/views/preview-job?id=$job->id" ?>`)'>
                                    Preview Job
                                  </button>

                                  <button type="button" class="dropdown-item" onclick='handleOpenModal(`<?= SERVER_NAME . "/public/views/preview-profile?id=$applicant->user_id" ?>`)'>
                                    Preview Profile
                                  </button>

                                  <?php if ($applicant->status == "Terminated" || $applicant->status == "Resigned") : ?>
                                    <button type="button" class="dropdown-item" onclick='handleRate(`<?= $applicant->user_id ?>`, `<?= $helpers->get_full_name($applicant->user_id) ?>`)'>
                                      Rate
                                    </button>
                                  <?php else : ?>
                                    <button type="button" class="dropdown-item" onclick='handleChangeStatus(`<?= $applicant->id ?>`, `Terminated`)'>
                                      Set Terminated
                                    </button>

                                    <button type="button" class="dropdown-item" onclick='handleChangeStatus(`<?= $applicant->id ?>`, `Resigned`)'>
                                      Set Resigned
                                    </button>
                                  <?php endif; ?>
                                </div>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- / Content -->

      </div>
      <!-- Content wrapper -->
    </div>
    <!-- / Layout page -->
  </div>

  <div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content" style="height:90vh">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col mb-3" style="height: 100%">
              <iframe src="" id="previewIframe" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Overlay -->
  <div class="layout-overlay layout-menu-toggle"></div>

</body>

<?php include("../components/footer.php") ?>

<script>
  function handleChangeStatus(candidateId, action) {
    swal.showLoading()
    $.post("<?= SERVER_NAME . "/backend/nodes?action=application_status_save" ?>", {
      candidate_id: candidateId,
      action: action
    }, (data, status) => {
      const resp = JSON.parse(data);

      swal.fire({
        title: resp.success ? "Success" : "Error",
        html: resp.message,
        icon: resp.success ? "success" : "error",
      }).then(() => {
        if (resp.success) {
          if (resp.id && resp.name) {
            handleRate(resp.id, resp.name)
          } else {
            window.location.reload()
          }
        }
      })

    });
  }

  function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
  }

  function handleRate(id, name) {
    swal.showLoading();

    getRateData(id).then((d) => {
      const rateData = $.parseJSON(d)

      let btnSoftSkillsStars = "";
      let btnCommunicationStars = "";
      let btnFlexibilityStars = "";

      for (let i = 1; i <= 5; i++) {
        let softSkillWarning = rateData && Number(rateData.soft_skills) >= i ? "btn-warning" : "";
        let softCommWarning = rateData && Number(rateData.communication) >= i ? "btn-warning" : "";
        let softFlexWarning = rateData && Number(rateData.flexibility) >= i ? "btn-warning" : "";

        btnSoftSkillsStars += `
                <button type="button" class="btn-soft-skills-${id} btn btn-outline-secondary btn-lg mx-1 ${softSkillWarning}" data-attr="${i}" id="${id}-soft-skills-star-${i}">
                  <i class="bx bxs-star" aria-hidden="true"></i>
                </button>`;

        btnCommunicationStars += `
                <button type="button" class="btn-communication-${id} btn btn-outline-secondary btn-lg mx-1 ${softCommWarning}" data-attr="${i}" id="${id}-communication-star-${i}">
                  <i class="bx bxs-star" aria-hidden="true"></i>
                </button>`;

        btnFlexibilityStars += `
                <button type="button" class="btn-flexibility-${id} btn btn-outline-secondary btn-lg mx-1 ${softFlexWarning}" data-attr="${i}" id="${id}-flexibility-star-${i}">
                  <i class="bx bxs-star" aria-hidden="true"></i>
                </button>`;

      }

      const rateHtml = `
            <input type="text" id="applicant_id_${id}" value="${id}" readonly hidden>
            <div class="row">
              <div class="form-group text-center mb-3" id="rating-ability-wrapper">
                <div class="text-start w-100">
                  <strong>Soft Skills</strong>

                  <div class="mt-2">
                    ${btnSoftSkillsStars}
                    <small class="bold rating-header">
                      <span class="soft-skills-rating-${id}">${rateData ? rateData.soft_skills : "0"}</span><small> / 5</small>
                    </small>
                  </div>
                </div>
                
                <input type="text" id="soft_skills${id}" value="${rateData ? rateData.soft_skills : ""}" name="soft_skill" hidden>
              </div>

              <div class="form-group text-center mb-3" id="rating-ability-wrapper">
                <div class="text-start w-100">
                  <strong>Communication</strong>

                  <div class="mt-2">
                    ${btnCommunicationStars}
                    <small class="bold rating-header">
                      <span class="communication-rating-${id}">${rateData ? rateData.communication : "0"}</span><small> / 5</small>
                    </small>
                  </div>
                </div>
                
                <input type="text" id="communication${id}" value="${rateData ? rateData.communication : ""}" name="communication" hidden>
              </div>

              <div class="form-group text-center mb-3" id="rating-ability-wrapper">
                <div class="text-start w-100">
                  <strong>Flexibility</strong>

                  <div class="mt-2">
                    ${btnFlexibilityStars}
                    <small class="bold rating-header">
                      <span class="flexibility-rating-${id}">${rateData ? rateData.flexibility : "0"}</span><small> / 5</small>
                    </small>
                  </div>
                </div>
                
                <input type="text" id="flexibility${id}" value="${rateData ? rateData.flexibility : ""}" name="flexibility" hidden>
              </div>
            </div>
            <div class="form-group mb-3">
              <label for="feedback" class="form-label">Feedback</label>
              <textarea class="form-control" id="feedback_${id}" name="feedback" rows="3" required>${rateData ? rateData.feedback : ""}</textarea>
            </div>`;

      swal.fire({
        title: `Rate ${name}`,
        html: rateHtml,
        confirmButtonText: "Submit",
        showDenyButton: true,
        denyButtonText: "Cancel",
        customClass: {
          htmlContainer: 'swal-custom-container',
        },
        allowOutsideClick: false,
        preConfirm: () => {
          if (!$(`#soft_skills${id}`).val() || !$(`#communication${id}`).val() || !$(`#flexibility${id}`).val()) {
            swal.showValidationMessage(`Please select rating`);
          } else if (!$(`#feedback_${id}`).val()) {
            swal.showValidationMessage(`Please add feedback`);
          } else {
            return true
          }
        }
      }).then((d) => {
        swal.close();
        swal.showLoading();

        if (d.isConfirmed) {
          const applicantId = $(`#applicant_id_${id}`).val()
          const softSkills = $(`#soft_skills${id}`).val()
          const communication = $(`#communication${id}`).val()
          const flexibility = $(`#flexibility${id}`).val()
          const feedback = $(`#feedback_${id}`).val()

          $.post(
            "<?= SERVER_NAME . "/backend/nodes?action=add_rating" ?>", {
              applicantId: applicantId,
              soft_skills: softSkills,
              communication: communication,
              flexibility: flexibility,
              feedback: feedback
            },
            (data, status) => {
              const resp = $.parseJSON(data)

              swal.fire({
                title: resp.success ? "Success" : "Error",
                html: resp.message,
                icon: resp.success ? "success" : "error",
              }).then(() => resp.success ? window.location.reload() : undefined)
            }
          )
        }
      })

      $(`.btn-soft-skills-${id}`).on('click', (function(e) {

        var previous_value = $(`#soft_skills${id}`).val();

        var selected_value = $(this).attr("data-attr");
        $(`#soft_skills${id}`).val(selected_value);

        $(`.soft-skills-rating-${id}`).empty();
        $(`.soft-skills-rating-${id}`).html(selected_value);

        for (i = 1; i <= selected_value; ++i) {
          $(`#${id}-soft-skills-star-${i}`).toggleClass('btn-warning');
        }

        for (ix = 1; ix <= previous_value; ++ix) {
          $(`#${id}-soft-skills-star-${ix}`).toggleClass('btn-warning');
        }
      }));

      $(`.btn-communication-${id}`).on('click', (function(e) {

        var previous_value = $(`#communication${id}`).val();

        var selected_value = $(this).attr("data-attr");
        $(`#communication${id}`).val(selected_value);

        $(`.communication-rating-${id}`).empty();
        $(`.communication-rating-${id}`).html(selected_value);

        for (i = 1; i <= selected_value; ++i) {
          $(`#${id}-communication-star-${i}`).toggleClass('btn-warning');
        }

        for (ix = 1; ix <= previous_value; ++ix) {
          $(`#${id}-communication-star-${ix}`).toggleClass('btn-warning');
        }
      }));

      $(`.btn-flexibility-${id}`).on('click', (function(e) {

        var previous_value = $(`#flexibility${id}`).val();

        var selected_value = $(this).attr("data-attr");
        $(`#flexibility${id}`).val(selected_value);

        $(`.flexibility-rating-${id}`).empty();
        $(`.flexibility-rating-${id}`).html(selected_value);

        for (i = 1; i <= selected_value; ++i) {
          $(`#${id}-flexibility-star-${i}`).toggleClass('btn-warning');
        }

        for (ix = 1; ix <= previous_value; ++ix) {
          $(`#${id}-flexibility-star-${ix}`).toggleClass('btn-warning');
        }
      }));
    })
  }

  async function getRateData(applicantId) {
    return await $.post(
      "<?= SERVER_NAME . "/backend/nodes?action=get_rating" ?>", {
        applicantId: applicantId
      },
      (data, status) => {
        return $.parseJSON(data)
      }
    )
  }

  function handleOpenModal(src) {
    $("#previewIframe").attr("src", src)
    $("#modalPreview").modal("show")
  }

  const hiredTableCols = [0, 1, 2, 3];
  const hiredTable = $("#hired-table").DataTable({
    paging: true,
    lengthChange: true,
    ordering: false,
    info: true,
    autoWidth: false,
    responsive: true,
    language: {
      searchBuilder: {
        button: 'Filter',
      }
    },
    buttons: [{
        extend: 'print',
        title: '',
        exportOptions: {
          columns: hiredTableCols
        },
        customize: function(win) {
          $(win.document.body)
            .css('font-size', '10pt')

          $(win.document.body)
            .find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        }
      },
      {
        extend: 'colvis',
        text: "Columns",
        columns: hiredTableCols
      },
      {
        extend: 'searchBuilder',
        config: {
          columns: hiredTableCols
        }
      }
    ],
    dom: `
      <'row'
      <'col-md-4 d-flex my-2 justify-content-start'B>
      <'col-md-4 d-flex my-2 justify-content-center'l>
      <'col-md-4 d-flex my-2 justify-content-md-end justify-content-sm-center'f>
      >
      <'row'<'col-12'tr>>
      <'row'
      <'col-md-6 col-sm-12'i>
      <'col-md-6 col-sm-12 d-flex justify-content-end'p>
      >
      `,
  });
</script>

</html>