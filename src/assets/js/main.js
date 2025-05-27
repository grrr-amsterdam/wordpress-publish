const Deployer = ($) => {
  const PAGE_SELECTOR = ".js-github-deploy";

  const $page = $(PAGE_SELECTOR);
  const $deployForm = $page.find(`form[data-type="github-deploy-form"]`);
  const $pollForm = $page.find(`form[data-type="github-poll-form"]`);
  const $messageContainer = $page.find(`div[data-type="status"]`);

  function setMessageType(status, conclusion) {
    if (isReady(status) && !hasFailed(conclusion)) {
      $messageContainer.removeClass("notice-info");
      $messageContainer.removeClass("error");
      $messageContainer.addClass("notice-success");
      return;
    }
    if (isReady(status) && hasFailed(conclusion)) {
      $messageContainer.removeClass("notice-info");
      $messageContainer.removeClass("notice-success");
      $messageContainer.addClass("error");
      return;
    }
    if (isPublishing(status)) {
      $messageContainer.removeClass("error");
      $messageContainer.removeClass("notice-success");
      $messageContainer.addClass("notice-info");
      return;
    }

    $messageContainer.removeClass("notice-info");
    $messageContainer.removeClass("notice-success");
    $messageContainer.addClass("error");
  }

  const formGetValue = (key, form) => {
    return form.serializeArray().find((input) => input.name === key).value;
  };

  const hasFailed = (conclusion) => {
    return conclusion === "failure";
  };

  const isPublishing = (status) => {
    return status !== "completed" && status !== "canceled";
  };

  const isReady = (status) => {
    return status === "completed";
  };

  const getStatusDescription = (status, conclusion) => {
    if (isReady(status) && !hasFailed(conclusion)) {
      return "Publication successful";
    }
    if (isReady(status) && hasFailed(conclusion)) {
      return "Publication failed";
    }
    if (isPublishing(status)) {
      return "Publication in progress";
    }

    return "Publication failed";
  };

  const formatDate = (date) => {
    return new Intl.DateTimeFormat("en-GB", {
      dateStyle: "long",
      timeStyle: "long",
    }).format(new Date(date));
  };

  const renderMessage = (status, updated_at, conclusion) => {
    $messageContainer.html(`
      <p>${getStatusDescription(status, conclusion)}</p>
      <p>${formatDate(updated_at)}</p>
    `);
    setMessageType(status, conclusion);
  };

  const renderError = (error) => {
    $messageContainer.html(`
      <p>Error: ${error}</p>
    `);
    setMessageType("error");
  };

  const post = (form) => {
    return new Promise((resolve, reject) => {
      $.ajax({
        method: "POST",
        url: form.attr("action"),
        beforeSend: (xhr) => {
          xhr.setRequestHeader("X-WP-Nonce", formGetValue("_wpnonce", form));
        },
      })
        .done(resolve)
        .fail((response) => {
          reject(
            response.responseJSON
              ? response.responseJSON.message
              : response.statusText,
          );
        });
    });
  };

  const disableForm = (form) => {
    $submitButton = form.find("button[type=submit]");
    $submitButton.prop("disabled", true);
  };

  const enableForm = (form) => {
    $submitButton = form.find("button[type=submit]");
    $submitButton.prop("disabled", false);
  };

  const poll = () => {
    return post($pollForm)
      .then(({ status, updated_at, conclusion }) => {
        renderMessage(status, updated_at, conclusion);
        if (!isPublishing(status)) {
          enableForm($deployForm);
        }
        setTimeout(poll, 5000);
      })
      .catch((error) => {
        renderError(error);
      });
  };

  const deploy = () => {
    disableForm($deployForm);
    return post($deployForm)
      .then(poll)
      .catch((error) => {
        renderError(error);
        enableForm($deployForm);
      });
  };

  const handlePollFormSubmit = (event) => {
    event.preventDefault();
    poll();
  };

  const handleDeployFormSubmit = (event) => {
    event.preventDefault();
    disableForm($deployForm);
    deploy();
  };

  return {
    init: () => {
      disableForm($deployForm);
      poll();
      $pollForm.on("submit", handlePollFormSubmit);
      $deployForm.on("submit", handleDeployFormSubmit);
    },
  };
};
jQuery(function ($) {
  const deployer = Deployer($);
  deployer.init();
});
