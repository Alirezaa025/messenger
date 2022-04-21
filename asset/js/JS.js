var inputField = document.getElementById("inputField");
var sendBtn = document.getElementById("newMessage");
var editBtn = document.getElementById("editMessage");

// dismiss_toast
var dismiss_toast = document.querySelectorAll("#dismiss_toast");
dismiss_toast.forEach((x) =>
  x.addEventListener("click", function () {
    x.parentElement.classList.add("hidden");
  })
);

// modal;
function modalHandler(val, id) {
  if (val == true) {
    $("#" + id)
      .css({ opacity: 0, display: "flex" })
      .animate(
        {
          opacity: 1,
        },
        300
      );
    var modal = document.getElementById(id);

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
      if (event.target == modal) {
        modalHandler(false, id);
      }
    };
  } else {
    $("#" + id).fadeOut(function () {
      if (id == "profile") {
        $("#editProf").css("display", "");
        $("#showProf").css("display", "block");
      }
    });
  }
}

//scroll to the bottom of "chatBody"
var chat = document.getElementById("chatBody");
if (chat) {
  chat.scrollTop = chat.scrollHeight;
}

//generate form of remove single message
function removeMessage(element) {
  const cancel = element.nextElementSibling;
  const submitDelete = element.firstElementChild;

  // hidden cancel if click on it
  cancel.style.display = null;
  cancel.addEventListener("click", () => {
    cancel.style.display = "none";
    submitDelete.setAttribute("disabled", "disabled");
  });

  // active delete button
  setTimeout(function () {
    submitDelete.value = submitDelete.removeAttribute("disabled");
  }, 100);
}

// edit selected message in input bar
function editMessage(element, id, show) {
  if (show == true) {
    const editMessageFrom = document.getElementById("editMessageFrom");
    const editField = document.getElementById("editField");
    const content = element.parentElement.firstElementChild;
    const message = content.innerText;
    document.getElementById("inputMessage").style.display = "none";
    editMessageFrom.style.display = "block";
    editBtn.style.display = "";

    editField.value = message;

    const inputId = document.createElement("input");
    inputId.type = "hidden";
    inputId.name = "messageID";
    inputId.value = id;

    editMessageFrom.appendChild(inputId);
  } else {
    editMessageFrom.style.display = "none";
    document.getElementById("inputMessage").style.display = "";
    document.getElementById("editMessageClose").style.display = "none";
    inputField.value = "";
  }
}

// preview image before upload
function previewImage(element, event) {
  const [file] = element.files;
  if (file) {
    const uploadField = element.parentElement;
    const field = uploadField.parentElement;

    const img = document.createElement("img");
    img.src = URL.createObjectURL(file);
    img.setAttribute(
      "style",
      "position:absolute; top: 0; width: 100%; height: 100%; object-fit: contain"
    );

    var preImg = uploadField.querySelector("img");
    if (preImg) {
      preImg.remove();
    }
    uploadField.appendChild(element);
    uploadField.appendChild(img);

    document.getElementById("cancelUpload").addEventListener("click", () => {
      img.remove();
      field.appendChild(uploadField);
    });
  }
}

// show sended image in bigger size
function biggerSize(element) {
  $(element).animate({ height: "+=12rem" });
  $(element).attr("onclick", "smallerSize(this)");
  $(element).removeClass("cursor-zoom-in");
  $(element).addClass("cursor-zoom-out");
}

// show sended image in smaller size
function smallerSize(element) {
  $(element).animate({ height: "-=12rem" });
  $(element).attr("onclick", "biggerSize(this)");
  $(element).removeClass("cursor-zoom-out");
  $(element).addClass("cursor-zoom-in");
}

// replace show profile info modal with editable info modal
function editProf() {
  $("#showProf").css("display", "none");
  $("#editProf").fadeIn();
}

// change attribute of upload image modal for upload profile image
function uploadProfImg(username) {
  $("#forWhere").remove();
  $("#forWhere").val(username);
  $("#typeOfInput").attr("name", "newProfImg");
  modalHandler(true, "uploadModal");
}

// show group info modal
function groupInfoModal() {
  $("#groupInfo").slideToggle();
}

// add selected emoji to target field
function addEmoji(emoji) {
  var target = "inputField";
  if ($("#inputMessage").css("display") === "none") {
    target = "editField";
  }

  $(`#${target}`).val(function () {
    return this.value + $(emoji).text();
  });
}

// carousel Handler
function carouselHandler(element, counter) {
  $(element).parent().css("display", "none");
  $(`#ProfImg_${counter}`).css("display", "block");
}

// add message to group messages
$("#inputMessage").submit(function (e) {
  e.preventDefault();
  const message = $("#inputField").val();
  if ([...message].length == 0) {
    alert("message cannot be empty");
    return;
  } else if ([...message].length > 100) {
  alert(`max length of message is 100 characters. your message contain ${message.length} characters`);
  return;
  }
  const groupID = $("input[name=groupID]").val();
  const userID = $("input[name=userID]").val();
  const main_url = $("input[name=main_url]").val();
  const rule = $("input[name=rule]").val();
  const messageType = "text";

  const messageDetails = {
    function: "addMessage",
    message: message,
    groupID: groupID,
    userID: userID,
  };
  $.post(
    `${main_url}controllers/message_functions.php`,
    messageDetails,
    function (error) {
      if (!error) {
        if (message.length == 0) {
          alert("message cannot be empty");
          return;
        } else if (message.length > 100) {
        alert(`max length of message is 100 characters. your message contain ${message.length} characters`);
        return;
        }
      }
      $("#inputField").val("");
    }
  );
  worker(groupID, userID, rule, main_url);
});

// read message of group from group db
function worker(groupID, userID, rule = "main", main_url) {
  const hash =
    document.cookie
      .match("(^|;)\\s*" + "groupMessageHash" + "\\s*=\\s*([^;]+)")
      ?.pop() || "";

  $.post(
    `${main_url}controllers/group_functions.php`,
    {
      function: "hashFileJS",
      groupID: groupID,
    },
    function (checkedHash) {
      if (hash != checkedHash) {
        $.post(
          `${main_url}controllers/message_functions.php`,
          {
            function: "readMessageJS",
            groupID: groupID,
            userID: userID,
            rule: rule,
            main_url: main_url,
          },
          function (response) {
            $("#chatBody").html(response);
          }
        );
        document.cookie = `groupMessageHash=${checkedHash}`;
        $("#chatBody").animate(
          { scrollTop: $("#chatBody").prop("scrollHeight") },
          500
        );
      }
    }
  );
}
