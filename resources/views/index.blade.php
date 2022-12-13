
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Cek Ongkir</title>

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <!-- Or for RTL support -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.ltr.min.css" />
  <style>
    .footer {
       position: fixed;
       left: 0;
       bottom: 0;
       width: 100%;
    }
  </style>  
</head>
<body>
  <div class="container mt-5">
    <h3 class="text-center">Cek Ongkos Kirim</h3>
    <h6 class="text-center mb-3">RajaOngkir</h6>
    <div class="row d-flex justify-content-center">
      <!-- form -->
      <div class="col-md-6" id="form">
        <form action="{{ url('/cek-ongkir') }}" method="POST">
          @csrf
          <div class="card">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Asal Pengiriman</label>
                <select class="form-select" id="asal" name="asal" data-placeholder="Pilih kota asal pengiriman" required>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Tujuan Pengiriman</label>
                <select class="form-select" id="tujuan" name="tujuan" data-placeholder="Pilih kota tujuan pengiriman" required>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Berat Barang</label>
                <div class="input-group">
                  <input type="number" class="form-control" name="berat" placeholder="Masukkan berat barang" required>
                  <span class="input-group-text" id="basic-addon2">gram</span>
                </div>
              </div>
              <div class="float-end">
                <button class="btn btn-primary" name="submit" id="submit">Cek Ongkir</button>
              </div>
            </div>
          </div>
        </form>
      </div>
      <!-- hasil -->
      <div class="col-md-8" id="hasil">
        <div class="card">
          <div class="card-body">
            <table class="table">
              <thead  class="table-light">
                <tr>
                  <th>Jasa Pengiriman</th>
                  <th>Jenis</th>
                  <th>Biaya Ongkir</th>
                  <th>Estimasi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- loading -->
    <div class="modal fade show bg-dark bg-opacity-25" data-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" id="loading">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-5">
          <div class="modal-body text-center">
            <div class="spinner-border mb-3" style="width: 2rem; height: 2rem;" role="status">
            </div>
            <h3>Loading...</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- footer -->
  <footer class="footer mt-auto py-3 bg-light text-center">
    <div class="container">
      <span class="text-muted">Made by Selvi Dwi Kartika</span>
    </div>
  </footer>
</body>
<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
  if ($('#form').hasClass('col-md-6')) {
    $('#hasil').hide();
  } else {
    $('#hasil').show();
  };
  
  // select2 kota asal pengiriman
  $('#asal').select2({
    theme: "bootstrap-5",
    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
    placeholder: $(this).data('placeholder'),
    ajax: {
      url: '/kota',
      minimumInputLength: 2,
      dataType: 'json',
      type: "get",
      delay: 250,
      processResults: function (data) {
        return {
          results: $.map(data, function (item) {
            return {
              text: item.city_name + ' ('+item.type+')',
              id  : item.id
            }
          })
        };
      },
      cache: true
    }
  });

  // select2 kota tujuan pengiriman
  $('#tujuan').select2( {
    theme: "bootstrap-5",
    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
    placeholder: $(this).data('placeholder'),
    ajax: {
      url: '/kota',
      minimumInputLength: 2,
      dataType: 'json',
      type: "get",
      delay: 250,
      processResults: function (data) {
        return {
          results: $.map(data, function (item) {
            return {
              text: item.city_name + ' ('+item.type+')',
              id  : item.id
            }
          })
        };
      },
      cache: true
    }
  });

  // cek ongkir
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $('#submit').on('click', function(e) {
    $('#loading').show();
    e.preventDefault();
    let asal   = $("select[name=asal]").val();
    let tujuan = $("select[name=tujuan]").val();
    let berat  = $("input[name=berat]").val();

    if ((asal===null) || (tujuan===null) || (berat==='')) {
      $('#loading').hide();
      return alert('Asal Pengiriman, Tujuan Pengiriman, dan Berat Barang wajib diisi');
    }
    $.ajax({
      url:"/cek-ongkir",
      type:'POST',
      data: {asal:asal, tujuan:tujuan, berat:berat},
      success: function(data) {
        $('#loading').hide();
        $('#form').removeClass('col-md-6');
        $('#form').addClass('col-md-4');
        $('#hasil').show();
        if ($('table tbody tr').length != 0) {
          $('table tbody').find("tr").remove();
        }
        $.each(data, function(index, item) {
          markup = '<tr><td>'+item.kurir.toUpperCase()+'</td><td>'+item.jenis+'</td><td>Rp'+item.ongkir.toLocaleString("id")+'</td><td>'+item.lama+' Hari</td></tr>';
          $('table tbody').append(markup);
        });
      },
      error: function(error) {
        $('#loading').hide();
        alert(error);
      }
    });
  });
</script>
</html>
