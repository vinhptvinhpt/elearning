export default {
    template: `
        <!-- The Modal -->
        <div id="popup_ticket" class="modal">
          <!-- Modal content -->
          <div class="modal-content">
            <span class="close" @click="closepopup">&times;</span>
            <img src="" width="400" height="400" style="margin-left: auto; margin-right: auto;">
          </div>
        </div>`,
    methods: {
        closepopup: function () {
            var popup = document.getElementById("popup_ticket");
            popup.style.display = "none";
        }
    }
}