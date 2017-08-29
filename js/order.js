function ajax() {
    var msg = $('#order-form').serialize();
    $.ajax({
        type: 'POST',
        url: '/php/form-handler.php',
        data: msg,
        success: function (data) {
            alert(data);
        }
    });
}
