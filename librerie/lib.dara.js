function postForm(path, params, method) {
    method = method || 'post';

    var form = document.createElement('form');
    form.setAttribute('method', method);
    form.setAttribute('action', path);

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            var hiddenField = document.createElement('input');
            hiddenField.setAttribute('type', 'hidden');
            hiddenField.setAttribute('name', key);
            hiddenField.setAttribute('value', params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function azioniDOMANDA(page,key)
{
  var params = {};
  params['_k'] = key;

  postForm(page, params);
}

function azioniAVVISO(page,key)
{
  var params = {};
  params['_k'] = key;

  postForm(page, params);
}

function isEmpty(obj) {

  if (obj == null) return true;
  if (obj.length === 0)  return true;
  if (obj==0)  return true;
  if (obj=="0")  return true;
  if (obj=="")  return true;
  for (var key in obj) {
    if (hasOwnProperty.call(obj, key)) return false;
  }

  return true;
}