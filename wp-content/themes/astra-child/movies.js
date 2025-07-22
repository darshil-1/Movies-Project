jQuery(document).ready(function ($) {
  var image_frame;
  $("#upload_images_button").on("click", function (e) {
    e.preventDefault();

    if (image_frame) {
      image_frame.open();
      return;
    }

    image_frame = wp.media({
      title: "Select Images",
      button: {
        text: "Add Images",
      },
      multiple: true,
    });

    image_frame.on("select", function () {
      var selection = image_frame.state().get("selection");
      var selected_images = [];
      selection.each(function (attachment) {
        selected_images.push(attachment.id);
      });

      $("#movie_images").val(selected_images.join(","));

      var preview = $("#movie_images_preview");
      preview.html("");
      selected_images.forEach(function (image_id) {
        preview.append(
          '<img src="' +
            wp.media.attachment(image_id).get("url") +
            '" class="thumbnail" />'
        );
      });
    });

    image_frame.open();
  });
});
