var skeletonId = 'skeleton';
var contentId = 'content';
var limit = 10;
// SUGGESTIONS VARS
var suggestionsOffset = 0;
// SENT REQ VARS
var sentReqOffset = 0;
var recReqOffset = 0;
var connectionOffset = 0;

function getRequests(mode) {
  if(mode == "sent_request_tab"){
    var queryString = "?limit="+limit+"&offset="+sentReqOffset
    var functionsOnSuccess = [
      [onSucsess, ["sentRequests", 'response']]
    ];
    ajax('/sent-requests'+queryString, 'GET', functionsOnSuccess);
  }else{
    var queryString = "?limit="+limit+"&offset="+recReqOffset
    var functionsOnSuccess = [
      [onSucsess, ["receivedRequests", 'response']]
    ];
    ajax('/received-requests'+queryString, 'GET', functionsOnSuccess);
  }
}

function getMoreRequests(mode) {
  getRequests(mode)
}

function getConnections() {
  var queryString = "?limit="+limit+"&offset="+connectionOffset
  var functionsOnSuccess = [
    [onSucsess, ["connections", 'response']]
  ];
  ajax('/connections'+queryString, 'GET', functionsOnSuccess);
}

function getMoreConnections() {
  getConnections()
}

function getSuggestions() {
  var queryString = "?limit="+limit+"&offset="+suggestionsOffset
  var functionsOnSuccess = [
    [onSucsess, ["suggestions", 'response']]
  ];
  ajax('/suggestions'+queryString, 'GET', functionsOnSuccess);
}

function getMoreSuggestions() {
  document.getElementById("suggestion_load_more_btn").disabled = true
  getSuggestions()
}

function sendRequest(requestId) {
  var form = ajaxForm([
    ['requestTo', requestId],
  ]);
  var functionsOnSuccess = [
    [() => {
      // REFRESH LIST
      $("#suggestion_tab").html("")
      suggestionsOffset = 0
      getSuggestions()
      connectionsCount()
    }, []]
  ];
  // POST 
  ajax('/send-request', 'POST', functionsOnSuccess, form);
}

function deleteRequest(connectionId) {
  var functionsOnSuccess = [
    [() => {
      // REFRESH LIST
      $("#sent_request_tab").html("")
      sentReqOffset = 0
      getRequests("sent_request_tab")
      connectionsCount()
    }, []]
  ];
  ajax('/withdraw-request?connectionId='+connectionId, 'DELETE', functionsOnSuccess);
}

function acceptRequest(connectionId) {
  var form = ajaxForm([
    ['connectionId', connectionId],
  ]);
  var functionsOnSuccess = [
    [() => {
      // REFRESH LIST
      $("#received_request_tab").html("")
      recReqOffset = 0
      getRequests("received_request_tab")
      connectionsCount()
    }, []]
  ];
  // POST 
  ajax('/connect', 'POST', functionsOnSuccess, form);
}

function removeConnection(connectionId) {
  var functionsOnSuccess = [
    [() => {
      // REFRESH LIST
      $("#connection_tab").html("")
      connectionOffset = 0
      getConnections()
      connectionsCount()
    }, []]
  ];
  ajax('/remove-connection?connectionId='+connectionId, 'DELETE', functionsOnSuccess);
}

function connectionsCount() {
  var functionsOnSuccess = [
    [onSucsess, ["connectionsCount", 'response']]
  ];
  ajax('/connections-counts', 'GET', functionsOnSuccess);
}

function showSkeleton(){
  document.getElementById("skeleton").classList.remove("d-none")
}

function hideSkeleton(){
  document.getElementById("skeleton").classList.add("d-none")
}

function setTab(tab){
  if(tab == "suggestion"){
    ["sent_request_tab", "received_request_tab", "connection_tab"].map((otherTab) => {
      document.getElementById(otherTab).classList.add("d-none")
      document.getElementById(otherTab).nextElementSibling.classList.add("d-none")
    })
    document.getElementById("suggestion_tab").classList.remove("d-none")
    $("#suggestion_tab").html("")
    suggestionsOffset = 0
    getSuggestions()
  }else if(tab == "sentRequest"){
    ["suggestion_tab", "received_request_tab", "connection_tab"].map((otherTab) => {
      document.getElementById(otherTab).classList.add("d-none")
      document.getElementById(otherTab).nextElementSibling.classList.add("d-none")
    })
    document.getElementById("sent_request_tab").classList.remove("d-none")
    $("#sent_request_tab").html("")
    sentReqOffset = 0
    getRequests("sent_request_tab")
  }else if(tab == "receivedRequest"){
    ["suggestion_tab", "sent_request_tab", "connection_tab"].map((otherTab) => {
      document.getElementById(otherTab).classList.add("d-none")
      document.getElementById(otherTab).nextElementSibling.classList.add("d-none")
    })
    document.getElementById("received_request_tab").classList.remove("d-none")
    $("#received_request_tab").html("")
    recReqOffset = 0
    getRequests("received_request_tab")
  }else if(tab == "connection"){
    ["suggestion_tab", "sent_request_tab", "received_request_tab"].map((otherTab) => {
      document.getElementById(otherTab).classList.add("d-none")
      document.getElementById(otherTab).nextElementSibling.classList.add("d-none")
    })
    document.getElementById("connection_tab").classList.remove("d-none")
    $("#connection_tab").html("")
    connectionOffset = 0
    getConnections()
  }
  showSkeleton()
}

function onSucsess(type, response) {
  // hide skeletons
  // show content
  if(type == "suggestions"){
    generateSuggestionsHtml(response)
  }else if (type == "connectionsCount"){
   $("#get_suggestions_btn").html(`Suggestions ${response.suggestionsCount}`)
   $("#get_sent_requests_btn").html(`Sent Requests ${response.sentRequestsCount}`)
   $("#get_received_requests_btn").html(`Received Requests ${response.receivedRequestsCount}`)
   $("#get_connections_btn").html(`Connections ${response.connectionsCount}`)
  }else if (type == "sentRequests"){
    generateRequestsHtml(response, "sent_request_tab")
  }else if (type == "receivedRequests"){
    generateRequestsHtml(response, "received_request_tab")
  }else if (type == "connections"){
    generateConnectionsHtml(response)
  }
}

function generateSuggestionsHtml(response){
  var listHtml = ""
  response.list.map((item) => {
    listHtml += ` <div class="d-flex justify-content-between mb-3">
    <table class="ms-1">
      <td class="align-middle">${item.name}</td>
      <td class="align-middle"> - </td>
      <td class="align-middle">${item.email}</td>
      <td class="align-middle"> 
    </table>
    <div>
      <button id="connect_btn" data-item-id='${item.id}' class="btn btn-primary me-1">Connect</button>
    </div>
  </div>`
  })
  $('#suggestion_tab').append(listHtml);
  document.getElementById("suggestion_tab").classList.remove("d-none")
  if(response.list.length == limit){
    document.getElementById("suggestion_load_more").classList.remove("d-none")
  }else{
    document.getElementById("suggestion_load_more").classList.add("d-none")
  }
  suggestionsOffset = suggestionsOffset + limit
  document.getElementById("suggestion_load_more_btn").disabled = false
  hideSkeleton()
}

function generateConnectionsHtml(response){
  var listHtml = ""
  response.map((item) => {
    listHtml += `<div>
    <div class="d-flex justify-content-between mb-3">
    <table class="ms-1">
      <td class="align-middle">${item.user.name}</td>
      <td class="align-middle"> - </td>
      <td class="align-middle">${item.user.email}</td>
      <td class="align-middle"> 
    </table>
    <div>
      <button style="width: 220px" id="get_connections_in_common_" class="btn btn-primary" type="button"
        data-bs-toggle="collapse" data-bs-target="#collapse_${item.connectionId}" aria-expanded="false" aria-controls="collapseExample">
        Connections in common (${item.commonConnections.length})
      </button>
      <button id="remove_connection_btn" data-item-id='${item.connectionId}' class="btn btn-danger me-1">Remove Connection</button>
    </div>
  </div>
    <div class="collapse" style="border: 1px solid grey; margin-bottom: 5px;"  id="collapse_${item.connectionId}">
      <p style="font-weight: bold;
        margin-left: 10px;
        margin-top: 10px;
        margin-bottom: 0;" >Common Connections</p>
      <div id="content_" class="p-2">
        ${item.commonConnections.map((common) => (
          `<p style="margin: 0" >${common.name} - ${common.email}</p>`
        ))}
      </div>
    </div>
  </div>`
  })
  $('#connection_tab').append(listHtml);
  document.getElementById("connection_tab").classList.remove("d-none")
  if(response.length == limit){
    document.getElementById("connection_load_more").classList.remove("d-none")
  }else{
    document.getElementById("connection_load_more").classList.add("d-none")
  }
  document.getElementById("connection_load_more_btn").disabled = false
  connectionOffset = connectionOffset + limit
  hideSkeleton()
}

function generateRequestsHtml(response, tab){
  var listHtml = ""
  response.map((item) => {
    var user = tab == "sent_request_tab" ? item.request_to_user : item.request_from_user
    listHtml += ` <div class="d-flex justify-content-between mb-3">
    <table class="ms-1">
      <td class="align-middle">${user.name}</td>
      <td class="align-middle"> - </td>
      <td class="align-middle">${user.email}</td>
      <td class="align-middle"> 
    </table>
    <div>
    ${tab == "sent_request_tab" ? (`<button id="cancel_request_btn_" class="btn btn-danger me-1" data-item-id='${item.id}' >Withdraw Request</button>
    `) : (`<button id="accept_request_btn" class="btn btn-primary me-1" data-item-id='${item.id}' onclick="">Accept</button>`)}
    </div>
  </div>`
  })
  $('#'+tab).append(listHtml);
  document.getElementById(tab).classList.remove("d-none")
  if(response.length == limit){
    document.getElementById(tab+"_load_more").classList.remove("d-none")
  }else{
    document.getElementById(tab+"_load_more").classList.add("d-none")
  }
  document.getElementById(tab+"_load_more_btn").disabled = false
  if(tab =="sent_request_tab"){
    sentReqOffset = sentReqOffset + limit
  }else{
    recReqOffset = recReqOffset + limit
  }
  hideSkeleton()
}

$(function () {
  connectionsCount()
  getSuggestions()
  $('#suggestion_tab').on('click', '#connect_btn' ,function(e){
    sendRequest(e.target.dataset.itemId)
  })
  $('#sent_request_tab').on('click', '#cancel_request_btn_' ,function(e){
    deleteRequest(e.target.dataset.itemId)
  })
  $('#received_request_tab').on('click', '#accept_request_btn' ,function(e){
    acceptRequest(e.target.dataset.itemId)
  })
  $('#connection_tab').on('click', '#remove_connection_btn' ,function(e){
    removeConnection(e.target.dataset.itemId)
  })
});