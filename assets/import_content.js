require(["core/first", "jquery", "jqueryui", "core/ajax"], function (
  core,
  $,
  bootstrap,
  ajax
) {
  $(document).ready(function () {
    $("#id_course_content").change(function () {
      // get current value then call ajax to get new data
      var selectedCourse = $("#id_course_content").val();
      if (selectedCourse == "") return;
      var selected_db = "global";
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
          console.log(response);
          // clear out old values
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
      // get current value then call ajax to get new data
      $("input[name = branch_hid]").val($("#id_branch").val());
      var selectedCourse = $("#id_course_content").val();
      var selected_db = "global";
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
    });
  });
});
