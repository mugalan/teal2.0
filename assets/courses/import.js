require(["core/first", "jquery", "jqueryui", "core/ajax"], function (
  _core,
  $,
  _bootstrap,
  ajax
) {
  $(document).ready(function () {
    $("#id_database").change(function () {
      // get current value then call ajax to get new data
      var selected_db = $("#id_database").val();

      ajax
        .call([
          {
            methodname: "external_calls_helpers_getCoursesFromDB",
            args: {
              db_type: selected_db,
            },
          },
        ])[0]
        .done(function (response) {
          // clear out old values
          $("#id_course").html("");

          var data = JSON.parse(response);
          $("<option/>")
            .val("none")
            .html("Select Course")
            .appendTo("#id_course");

          for (var i = 0; i < data.length; i++) {
            if (
              data[i].substring(0, 13) !== "CourseContent" &&
              data[i].substring(0, 7) !== "Program"
            ) {
              var val = data[i].substring(7);
              $("<option/>").val(data[i]).html(val).appendTo("#id_course");
            }
          }

          return;
        })
        .fail(function (err) {
          console.log(err);
          return;
        });
    });

    $("#id_course").change(function () {
      $("input[name = repo_name]").val($("#id_course").val());
      var selectedCourse = $("#id_course").val();
      var selected_db = $("#id_database").val();

      ajax
        .call([
          {
            methodname: "external_calls_helpers_getBranchesForCourse",
            args: {
              selected_course: selectedCourse,
              db_type: selected_db,
            },
          },
        ])[0]
        .done(function (response) {
          $("#id_branch").html("");

          var data = JSON.parse(response);

          $("<option/>")
            .val("none")
            .html("Select Branch")
            .appendTo("#id_branch");

          for (var i = 0; i < data.length; i++) {
            var vals = data[i]["ref"].split("/");
            var val = vals[vals.length - 1];
            $("<option/>").val(val).html(val).appendTo("#id_branch");
          }

          return;
        })
        .fail(function (err) {
          console.log(err);
          return;
        });
    });
    $("#id_branch").change(function () {
      $("input[name = branch_hid]").val($("#id_branch").val());

      var selectedCourse = $("#id_course").val();
      var selected_db = $("#id_database").val();
      var selected_branch = $("#id_branch").val();

      ajax
        .call([
          {
            methodname: "external_calls_helpers_getCommitsForBranches",
            args: {
              selected_branch: selected_branch,
              selected_course: selectedCourse,
              db_type: selected_db,
            },
          },
        ])[0]
        .done(function (response) {
          console.log(response);
          // clear out old values
          $("#id_commit").html("");

          var data = JSON.parse(response);

          $("<option/>")
            .val("none")
            .html("Select Version")
            .appendTo("#id_commit");

          for (var i = 0; i < data.length; i++) {
            $("<option/>")
              .val(data[i]["sha"])
              .html(data[i]["commit"]["message"])
              .appendTo("#id_commit");
          }

          return;
        })
        .fail(function (err) {
          console.log(err);
          return;
        });
    });
    $("#id_commit").change(function () {
      $("input[name = commit_hid]").val($("#id_commit").val());
      var selectedCourse = $("#id_course").val();
      var selected_db = $("#id_database").val();
      var selected_branch = $("#id_branch").val();
      var selected_commit = $("#id_commit").val();

      ajax
        .call([
          {
            methodname: "external_calls_helpers_getCourse",
            args: {
              commit: selected_commit,
              selected_course: selectedCourse,
              db_type: selected_db,
            },
          },
        ])[0]
        .done(function (response) {
          console.log(response);
          var data = JSON.parse(response);

          $("#id_code").val(data["courseCode"]);
          $("#id_name").val(data["courseName"]);
          $("#id_type").val(data["courseType"]);
          $("#id_category").val(data["courseCategory"]);
          $("#id_level").val(data["courseLevel"]);
          $("#id_credits").val(data["courseCredits"]);
          $("#id_credits").val(data["courseCredits"]);
          $("input[name = learning_outcomes]").val(
            data["courseLearningOutcomes"].join(";")
          );
        })
        .fail(function (err) {
          console.log(err);
          return;
        });

      console.log(selectedCourse + " " + selected_db);
    });
  });
});
