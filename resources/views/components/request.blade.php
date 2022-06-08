<div class="my-2 shadow text-white bg-dark p-1"  >

<div id="{{$mode}}" class="d-none" style="height: 500px; overflow-y: scroll;" >
</div>
  <!-- <div class="d-flex justify-content-between">
    <table class="ms-1">
      <td class="align-middle">Name</td>
      <td class="align-middle"> - </td>
      <td class="align-middle">Email</td>
      <td class="align-middle">
    </table>
    <div>
      @if ($mode == 'sent_request_tab')
        <button id="cancel_request_btn_" class="btn btn-danger me-1"
          onclick="">Withdraw Request</button>
      @else
        <button id="accept_request_btn_" class="btn btn-primary me-1"
          onclick="">Accept</button>
      @endif
    </div>
  </div> -->
  <div class="d-flex justify-content-center mt-2 py-3 d-none" id=<?php echo $mode."_load_more"?> >
    <button class="btn btn-primary" onclick="getMoreRequests('<?php echo $mode ?>')" id=<?php echo $mode."_load_more_btn"?> >Load more</button>
  </div>
</div>
