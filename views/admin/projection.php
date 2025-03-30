<?php include("../../backend/nodes.php"); ?>
<?php include("../../components/function_components.php"); ?>
<?php
if (!isset($_SESSION["id"])) {
  header("location: ../sign-in");
}

$LOGIN_USER = $helpers->get_user_by_id($_SESSION["id"]);
$pageName = "Projection & Prediction";

$industries = $helpers->select_all("industries");
?>
<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-template="vertical-menu-template-free">

<?= head($pageName) ?>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <?php include("../../components/sidebar.php") ?>

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <?php include("../../components/navbar.php") ?>
        <!-- / Navbar -->

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
              <div class="col-md-12">
                <ul class="nav nav-pills px-4" role="tablist">
                  <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-projection" aria-controls="tab-projection" aria-selected="true">
                      <i class="bx bx-line-chart me-1"></i>
                      Projection
                    </button>
                  </li>
                  <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-prediction" aria-controls="tab-prediction" aria-selected="true">
                      <i class='bx bx-spreadsheet me-1'></i>
                      Prediction
                    </button>

                  </li>

                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="tab-projection" role="tabpanel">
                    <div class="card mb-4">
                      <div class="card-body">
                        <div class="row justify-content-end">
                          <div class="col-md-7">
                            <div class="row">
                              <div class="col-3">
                                <label for="quarter" class="form-label">Quarter</label>
                                <select id="quarter" class="form-select">
                                  <option value="" selected disabled></option>
                                  <option value="1">1st quarter</option>
                                  <option value="2">2nd quarter</option>
                                  <option value="3">3rd quarter</option>
                                  <option value="4">4th quarter</option>
                                  <option value="5">Whole Year</option>
                                </select>
                              </div>
                              <div class="col-7">
                                <label for="industry_id" class="form-label">Industry</label>
                                <select id="industry_id" class="form-select">
                                  <option value="" selected disabled></option>
                                  <?php
                                  if (count($industries) > 0):
                                    foreach ($industries as $industry):
                                  ?>
                                      <option value="<?= $industry->id ?>"><?= $industry->name ?></option>
                                    <?php endforeach; ?>
                                  <?php endif; ?>
                                </select>
                              </div>
                              <div class="col-2">
                                <label for="year" class="form-label">Year</label>
                                <select id="year" class="form-select">
                                  <option value="" selected disabled></option>
                                  <?php for ($i = date("Y"); $i > 2018; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                  <?php endfor ?>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="row mt-4">
                          <div class="col-md-6">
                            <div class="card">
                              <div class="card-body">
                                <p>
                                  By Quarter:
                                  <span id="byQuarter">--</span>
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="card">
                              <div class="card-body">
                                <p>
                                  By Year:
                                  <span id="byYear">--</span>
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="chart" class="mt-4"></div>
                      </div>
                    </div>

                  </div>
                  <div class="tab-pane fade" id="tab-prediction" role="tabpanel">
                    <div class="row justify-content-center">
                      <div class="col-md-12">
                        <div class="card">
                          <div class="card-body">
                            <div class="row justify-content-end">
                              <div class="col-md-6">
                                <div class="row justify-content-end">
                                  <!-- <div class="col-3">
                                    <label for="predictionMonth" class="form-label">Month</label>
                                    <select id="predictionMonth" class="form-select">
                                      <option value="" selected disabled></option>
                                      <?php
                                      $months = [
                                        "January",
                                        "February",
                                        "March",
                                        "April",
                                        "May",
                                        "June",
                                        "July",
                                        "August",
                                        "September",
                                        "October",
                                        "November",
                                        "December"
                                      ];
                                      foreach ($months as $key => $month):
                                      ?>
                                        <option value="<?= $key ?>"><?= $month ?></option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>
                                  <div class="col-2">
                                    <label for="predicationYear" class="form-label">Year</label>
                                    <select id="predicationYear" class="form-select">
                                      <option value="" selected disabled></option>
                                      <?php for ($i = date("Y"); $i > 2018; $i--): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                      <?php endfor ?>
                                    </select>
                                  </div> -->
                                  <div class="col-7">
                                    <label for="predictionIndustry" class="form-label">Industry</label>
                                    <select id="predictionIndustry" class="form-select">
                                      <option value="" selected disabled></option>
                                      <?php
                                      if (count($industries) > 0):
                                        foreach ($industries as $industry):
                                      ?>
                                          <option value="<?= $industry->id ?>"><?= $industry->name ?></option>
                                        <?php endforeach; ?>
                                      <?php endif; ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <table id="forecasting-table" class="table table-striped nowrap">
                              <thead>
                                <tr>
                                  <th>Company name</th>
                                  <th>Vacancies</th>
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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
  <!-- Overlay -->
  <div class="layout-overlay layout-menu-toggle"></div>
</body>

<?php include("../../components/footer.php") ?>
<script src="<?= SERVER_NAME . "/assets/vendor/libs/apexcharts/apexcharts.js" ?>"></script>
<script>
  var options = {
    series: [{
      name: "Job Post(s)",
      data: []
    }],
    chart: {
      height: 350,
      type: 'line',
      zoom: {
        enabled: false
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'straight'
    },
    grid: {
      row: {
        colors: ['#f3f3f3'],
        opacity: 0.5
      },
    },
    yaxis: {
      labels: {
        formatter: (val) => val
      },
    },
    tooltip: {
      y: {
        formatter: (val) => val
      }
    },
    xaxis: {
      categories: [],
    }
  };

  var chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();

  $("#quarter, #industry_id, #year").on("change", function() {
    updateChart()
  })

  $("#predictionIndustry").on("change", function() {
    updateTable()
  })

  function updateTable() {
    // const month = $("#predictionMonth").val()
    // const year = $("#predicationYear").val()
    const industry_id = $("#predictionIndustry").val()

    // if (month && year && industry_id) {
    if (industry_id) {
      const fd = new FormData();
      // fd.append("month", month);
      // fd.append("year", year);
      fd.append("industry_id", industry_id);

      $.ajax({
        url: "<?= SERVER_NAME . "/backend/nodes?action=get_forecasting" ?>",
        method: "POST",
        data: fd,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "JSON",
        success: (data) => {
          if (data.table_data) {
            $("#forecasting-table tbody").html(data.table_data)
          } else {
            $("#forecasting-table tbody").html("<tr><td colspan='2' class='text-center'>No data found</td></tr>")
          }
        }
      })
    }
  }

  async function updateChart() {
    const quarter = $("#quarter").val();
    const industry_id = Number($("#industry_id").val())
    const year = $("#year").val()

    if (quarter && industry_id && year) {
      const lineData = await getLineChartData(quarter, industry_id, year)

      const newOptions = options;
      newOptions.series[0].data = Object.values(lineData.line_data)
      newOptions.xaxis.categories = Object.keys(lineData.line_data)

      chart.updateOptions(newOptions);
      $("#byQuarter").html(lineData.quarter)
      $("#byYear").html(lineData.year)
    }
  }

  function getLineChartData(quarter, industryId, year) {
    const fd = new FormData();
    fd.append("quarter", quarter);
    fd.append("industry_id", industryId);
    fd.append("year", year);

    return $.ajax({
      url: "<?= SERVER_NAME . "/backend/nodes?action=get_line_chart" ?>",
      method: "POST",
      data: fd,
      contentType: false,
      cache: false,
      processData: false,
      dataType: "JSON",
      success: (data) => data
    });
  }
</script>

</html>