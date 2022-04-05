require(["core/first", "jquery", "jqueryui", "core/ajax"], function (
  core,
  $,
  bootstrap,
  ajax
) {
  $(document).ready(function () {
    id = 1;
    $("#id_add_course_learning_outcomes").click(function () {
      var textfield = $(`#fitem_id_course_learning_outcomes_${id}`).clone();
      id++;

      textfield.attr("id", `fitem_id_course_learning_outcomes_${id}`);

      textfield.insertAfter($(`#fitem_id_course_learning_outcomes_${id - 1}`));

      $(`#fitem_id_course_learning_outcomes_${id}`)
        .find(`#id_course_learning_outcomes_${id - 1}`)
        .attr("id", `id_course_learning_outcomes_${id}`);

      $(`#id_course_learning_outcomes_${id - 1}`).prop("disabled", true);

      prevVal = $("input[name = course_learning_outcomes]").val();
      prevVal = prevVal === "" ? prevVal : prevVal + ";";
      newVal = prevVal + $(`#id_course_learning_outcomes_${id - 1}`).val();
      console.log(newVal);

      $(`#id_course_learning_outcomes_${id}`).val("");
      $("input[name = course_learning_outcomes]").val(newVal);
    });
  });
});
