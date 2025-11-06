// assets/js/goa-deactivation.js
jQuery(document).ready(function ($) {
  // Find the deactivation link for our plugin
  var deactivateLink = $(
    'tr[data-plugin="' + goaDeactivation.pluginSlug + '"] .deactivate a'
  );

  deactivateLink.on("click", function (e) {
    e.preventDefault();
    var originalUrl = $(this).attr("href");

    // Create modal HTML (you can customize reasons)
    var modalHtml = `
            <div id="goa-deactivation-modal" class="goa-modal">
                <div class="goa-modal-content">
                    <h2>Quick Feedback</h2>
                    <p>If you have a moment, please let us know why you are deactivating:</p>
                    <form id="goa-feedback-form">
                        <label><input type="radio" name="reason" value="temporary"> It's a temporary deactivation</label>
                        <label><input type="radio" name="reason" value="not-working"> The plugin didn't work as expected</label>
                        <label><input type="radio" name="reason" value="found-better"> I found a better plugin</label>
                        <label><input type="radio" name="reason" value="missing-feature"> Missing a feature I need</label>
                        <label><input type="radio" name="reason" value="other"> Other</label>
                        <textarea name="details" placeholder="Optional details..." style="display:none;" required></textarea>
                    </form>
                    <div class="goa-modal-buttons">
                        <button id="goa-skip-feedback">Skip & Deactivate</button>
                        <button id="goa-submit-feedback">Submit & Deactivate</button>
                    </div>
                </div>
            </div>
        `;

    // Append modal to body and show
    $("body").append(modalHtml);
    var modal = $("#goa-deactivation-modal");
    modal.fadeIn();

    // Show details textarea if "other" or specific reasons selected
    $('input[name="reason"]').on("change", function () {
      if (
        $(this).val() === "other" ||
        $(this).val() === "not-working" ||
        $(this).val() === "missing-feature"
      ) {
        $('textarea[name="details"]').show();
      } else {
        $('textarea[name="details"]').hide();
      }
    });

    // Function to validate form
    function isValid() {
      var reason = $('input[name="reason"]:checked').val();
      var details = $('textarea[name="details"]').val().trim();

      if (!reason) {
        alert("Please select a reason.");
        return false;
      }

      if (reason === "other" && !details) {
        alert("Please provide details for deactivating.");
        return false;
      }

      return true;
    }

    // Submit feedback
    $("#goa-submit-feedback").on("click", function () {
      if (!isValid()) {
        return;
      }
      var reason = $('input[name="reason"]:checked').val();
      var details = $('textarea[name="details"]').val();

      // Disable button to prevent double-click
      $(this).prop("disabled", true).text("Submitting...");

      $.ajax({
        url: goaDeactivation.feedbackUrl,
        type: "POST",
        data: {
          action: goaDeactivation.action,
          nonce: goaDeactivation.nonce,
          reason: reason,
          details: details,
          plugin: "guest-order-assigner",
        },
        success: function () {
          window.location.href = originalUrl;
        },
        error: function () {
          window.location.href = originalUrl; // Proceed even on error
        },
      });
    });

    // Skip feedback
    $("#goa-skip-feedback").on("click", function () {
      window.location.href = originalUrl;
    });

    // Close modal on outside click (optional)
    $(window).on("click", function (e) {
      if (e.target === modal[0]) {
        modal.remove();
      }
    });
  });

  $(document).on("click", ".goa-promo-notice .notice-dismiss", function () {
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "goa_dismiss_promo_notice",
        nonce: goaPromo.deactivationNonce,
      },
    });
  });
});
