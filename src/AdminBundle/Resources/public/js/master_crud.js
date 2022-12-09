(function ($) {
  var modalSubscriberWindow = $("#subscriber-modal");
  var modalSubscriberForm = modalSubscriberWindow.find("form");

  $("#subscriber-add").click(function () {
    modalSubscriberWindow.modal("show");
  });

  modalSubscriberForm.submit(function (event) {
    event.preventDefault();
    event.stopPropagation();

    clearErrors(modalSubscriberForm);

    $.ajax({
      method: "POST",
      url: this.getAttribute("action"),
      processData: false,
      contentType: false,
      data: new FormData(this),
    })
      .done(function () {
        window.location.reload();
      })
      .fail(function (jqXHR) {
        var errors = jqXHR.responseJSON.errors;

        for (var idx in errors) {
          if (errors.hasOwnProperty(idx)) {
            var error = errors[idx];

            attachError(error.field, error.message);
          }
        }
      });
  });

  function clearErrors(form) {
    if (typeof form === "string") {
      form = $(form);
    }

    form.find(".help-block ul").html("");
  }

  function attachError(field, message) {
    var view = undefined;

    if (field === "subscriber_masterUser") {
      view = $("#subscriber").find("> .help-block ul");
    } else {
      view = $("#" + field)
        .parent()
        .find(".help-block ul");
    }
    view.append(
      '<li><span className="glyphicon glyphicon-exclamation-sign"></span>' +
        message +
        "</li>"
    );
  }
})(jQuery);
