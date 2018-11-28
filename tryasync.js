function asyncRequest() {
  try {
    // Non-IE browser
    var request = new XMLHttpRequest();
  } catch(e1) {
    try {
      // IE 6+
      request = new ActiveXObject("Msxml.XMLHTTP");
    } catch(e2) {
      try {
        // IE 5
        request = new ActiveXObject("Microsoft.XMLHTTP");
      } catch(e3) {
        // There is no async support
        request = false
      }
    }
  }
  return request
}
