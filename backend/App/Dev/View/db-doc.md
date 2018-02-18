## structure syntax (write in yaml format)

* element: _element type_ `input,radios,select,tags,textarea,checkbox[es],file,hidden`
* type: _input type_ `text,password,number,email,tel,url,[date][time][-local],month,week,search,color`
* label: _Displayed before element or in placeholder_
* placeholder: _Diplayed in placeholder_
* desc: _little popup or long line description_
* required: _1 or 0_
* filters: _list of filters names/shortcuts + options_
  * `<filter>`: `[option1,option2,...]` 
* validators: _list of validators names/shortcuts + options_
  * `<validator>`: `[option1,option2,...]` 
* left: _left label_
  * label: _short label_
  * icon: _fa icon without `fa-`_
* acl: _Restriction to acl role(s)_
* mask: _input mask name_
* maskopt:
  * `<optkey>`: `optvalue`
* options: _options list or SQL_
* default: _default value_
* autocomplete: _options list or SQL_
* size: _size of the field (1-12)_
* css: _CSS classes_ `['class1','class2',...]`
* attrs: _HTML attributes_
  * `key: value`
* tooltip: _tooltip message_

## Examples

    element: input
    type: password
    label: Mot de passe
    placeholder: mot de passe
    desc: Au moins 6 chiffres et lettres
    required: 1
    validators: 
      len: 6
    left:
      icon: key
    acl: ADMIN

    mask: 99/99/9999
    maskopt:
      placeholder: dd/mm/yyyy
