
function ttShowAddressForm(e) {
    e.preventDefault();
    document.getElementById('tt-address-form').style.display = 'block';
}
function ttHideAddressForm() {
    document.getElementById('tt-address-form').style.display = 'none';
}
function ttSaveAddress(e) {
    e.preventDefault();
    // Lấy giá trị từ form
    var name = document.getElementById('tt-input-name').value;
    var phone = document.getElementById('tt-input-phone').value;
    var address = document.getElementById('tt-input-address').value;
    var city = document.getElementById('tt-input-city').value;
    // Update ra ngoài view
    document.getElementById('tt-user-name').innerText = name;
    document.getElementById('tt-user-phone').innerText = phone;
    document.getElementById('tt-user-address').innerText = address + ', ' + city;
    // Lưu vào input hidden để form submit thanh toán lấy giá trị mới
    document.getElementById('tt-hidden-address').value = address;
    document.getElementById('tt-hidden-city').value = city;
    document.getElementById('tt-hidden-name').value = name;
    document.getElementById('tt-hidden-phone').value = phone;
    // Ẩn form lại
    ttHideAddressForm();
    return false;
}
function ttShowAddressForm(e) {
    if(e) e.preventDefault();
    document.getElementById('tt-address-form').style.display = 'block';
}
function ttHideAddressForm() {
    document.getElementById('tt-address-form').style.display = 'none';
}

function ttSaveAddressAjax() {
    var name    = document.getElementById('tt-input-name').value;
    var phone   = document.getElementById('tt-input-phone').value;
    var address = document.getElementById('tt-input-address').value;
    var city    = document.getElementById('tt-input-city').value;
    var csrf    = document.querySelector('input[name="_token"]').value;

    fetch(window.ttUpdateAddressUrl, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        ten_nguoi_dung: name,
        sdt: phone,
        dia_chi: address,
        tinh_thanh: city
    })
})
    .then(res => res.json())
    .then(json => {
        if(json.success) {
            // Update giao diện hiển thị thông tin mới
            document.getElementById('tt-user-name').innerText = name;
            document.getElementById('tt-user-phone').innerText = phone;
            document.getElementById('tt-user-address').innerText = address + ', ' + city;

            // Update các input hidden để form thanh toán submit đúng
            document.getElementById('tt-hidden-name').value = name;
            document.getElementById('tt-hidden-phone').value = phone;
            document.getElementById('tt-hidden-address').value = address;
            document.getElementById('tt-hidden-city').value = city;

            ttHideAddressForm();
            alert('Cập nhật thành công!');
        } else {
            alert(json.msg || 'Có lỗi xảy ra!');
        }
    })
    .catch(err => {
        alert('Có lỗi xảy ra!');
        console.error(err);
    });
}



