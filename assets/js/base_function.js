"use strict";

$(function () {
  $("#failed_modal").modal();
  $("#success_modal").modal();
});

var logg_status = 1;
var unsaved = false;

function unloadPage() {
  if (unsaved) {
    return "Apakah anda yakin akan meninggalkan laman ini?";
  }
}

window.onbeforeunload = unloadPage;

var proses_ = {
  status: 0,
  status_and_mulai: function () {
    // console.log('status & mulai');
    var def = proses_.status;
    proses_.mulai();
    return def;
  },
  mulai: function () {
    // console.log('mulai');
    proses_.status = 1;
  },
  selesai: function () {
    // console.log('selesai');
    proses_.status = 0;
  },
};

//done typing function ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
$.fn.donetyping = function (callback, time) {
  time = time != undefined ? time : 1500;
  /*
    $('.class').donetyping(function(){
        //action here
      });
  */
  var _this = $(this);
  var x_timer;
  _this.keyup(function () {
    clearTimeout(x_timer);
    x_timer = setTimeout(clear_timer, time);
  });

  function clear_timer() {
    clearTimeout(x_timer);
    callback.call(_this);
  }
};

$.fn.hasAttr = function (attr) {
  var attr = $(this).attr(attr);
  // For some browsers, `attr` is undefined; for others, `attr` is false. Check for both.
  return typeof attr !== typeof undefined && attr !== false ? 1 : 0;
};

function notif_success(judul, isi) {
  notif({
    msg: "<b>" + judul + ": </b>" + isi,
    type: "primary",
    position: "center",
  });
}

function notif_error(judul, isi) {
  notif({
    type: "error",
    msg: "<b>" + judul + ": </b>" + isi,
    position: "bottom",
    bottom: "10",
  });
}

Number.prototype.uang = function uang() {
  if (!this || this == "") return 0;
  return this.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

String.prototype.nominal = function nominal() {
  if (!this || this == "") return 0;
  if (this.indexOf(",") > -1) return parseInt(this.replace(/,/g, ""));
  else return parseInt(this);
};

function goto(url, new_tab) {
  if (new_tab != undefined) window.open(url, "_blank");
  else window.location.href = url;
}

//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
//::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

// $(function () {
//   $(".checkbox-inline").each(function (i, v) {
//     var text = $(this).find("p");
//     if ($(this).find('input[type="checkbox"]').is(":checked"))
//       text.text(text.attr("data-on"));
//     else text.text(text.attr("data-off"));
//   });

//   $('.checkbox-inline input[type="checkbox"]').change(function () {
//     var text = $(this).closest(".checkbox-inline").find("p");
//     if ($(this).is(":checked")) text.text(text.attr("data-on"));
//     else text.text(text.attr("data-off"));
//   });
// });

function alert_mobile(str, fn) {
  if (typeof fn === "function") {
    mcxDialog.confirm(str, {
      sureBtnClick: fn,
    });
  } else {
    mcxDialog.alert(str);
  }
}

function notif_mobile(str) {
  mcxDialog.toast(str, 2);
}

function escapeHtml(text) {
  var map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };

  return text.replace(/[&<>"']/g, function (m) {
    return map[m];
  });
}

function share(judul, text, url) {
  if (!navigator.canShare) {
    alert(`Browser tidak support share, gunakan copy link.`);
    return;
  }

  if (navigator.share) {
    navigator
      .share({
        title: judul,
        text: text,
        url: url,
      })
      .then(() => console.log("Successful share"))
      .catch((error) => console.log("Error sharing:", error));
  }
}

function micro_id() {
  const timestamp = new Date().getTime();
  const randomNum = Math.floor(Math.random() * 1000000); // You can adjust the range as needed

  const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let code = "";

  for (let i = 0; i < 5; i++) {
    const randomIndex = Math.floor(Math.random() * charset.length);
    code += charset.charAt(randomIndex);
  }

  return timestamp + code;
}

function copy_text(text) {
  navigator.clipboard.writeText(text);
}

function getLatLong(str) {
  //koordinat polos
  str = decodeURIComponent(str.replace(/\+/g, " "));
  var regexExp = /^(-?\d+(\.\d+)?),\s*(-?\d+(\.\d+)?)$/gi;
  if (regexExp.test(str)) {
    var [lat, long] = str.split(",");
    return { lat: parseFloat(lat), lng: parseFloat(long) };
  }

  //ling google
  var regexExp = /@([\d\.]+),([\d\.]+)/;
  var match = str.match(regexExp);
  if (match) {
    return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
  }

  //ling wa
  var regexExp = /q=([\d\.]+),([\d\.]+)/;
  var match = str.match(regexExp);
  if (match) {
    return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
  }

  return null;
}

function logg(str, logic) {
  if (typeof logic !== "undefined" && logic !== null) {
    if (logic) logg_status = 1;
    else logg_status = 0;
  }

  if (logg_status) {
    const callerStack = new Error().stack.split("\n");
    // Kami mengambil baris ke-3 dalam tumpukan (indeks 2) karena baris ke-1 adalah informasi kesalahan itu sendiri
    const callerLine = callerStack[2];
    console.log("trace :", callerLine);
    console.log("========= OUTPUT ==========");
    console.log(str);
    console.log("\n");
  }
}

function reload() {
  location.reload();
}

function htmlspecialchars(input) {
  if (typeof input === "string") {
    return input
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  } else {
    return input;
  }
}
function decodehtmlspecialchars(input) {
  var doc = new DOMParser().parseFromString(input, "text/html");
  return doc.documentElement.textContent;
}

function tanggal(dateString) {
  const options = { day: "numeric", month: "long", year: "numeric" };
  const date = new Date(dateString);

  return new Intl.DateTimeFormat("id-ID", options).format(date);
}
