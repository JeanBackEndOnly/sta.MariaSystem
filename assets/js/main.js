$(document).ready(function () {
  

  $('html').css('scroll-behavior', 'smooth')
  $('#systemLogo').on('change', function (event) {
    const fileInput = event.target
    const preview = $('.preview')

    preview.empty()

    const files = fileInput.files
    for (const file of files) {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = function (e) {
          preview.attr('src', e.target.result)
          $('input[name=system_logo]').attr('value', e.target.result)
        }
        reader.readAsDataURL(file)
      } else {
        const para = $('<p>').text(
          `File ${file.name} is not a valid image file.`
        )
        preview.append(para)
      }
    }
  })

  $('body').on('click', '#logout', function (e) {
    e.preventDefault()
    const $this = $(this)
    $.ajax({
      url: base_url + 'authentication/action.php?action=logout',
      method: 'POST',
      dataType: 'json',
      beforeSend: function () {
        $this.text('Logging out...')
      },
      success: function (response) {
        if (response.status == 1) {
          Swal.fire({
            title: 'Success!',
            text: response.message,
            icon: 'success',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
          }).then(() => {
            window.location.href =
              base_url + (response.redirect_url || 'index.php')
          })
        } else {
          showError(response.message)
        }
      },
      error: function () {
        console.error('AJAX error')
      }
    })
  })

  function showError (message) {
    Swal.fire({
      title: 'Failed',
      text: message,
      icon: 'error',
      toast: true,
      position: 'top-end',
      timer: 3000,
      showConfirmButton: false
    })
    //$form.removeClass("processing");
  }
})
