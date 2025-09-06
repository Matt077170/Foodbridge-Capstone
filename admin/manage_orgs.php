<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Organizations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="manageorgs.css"/>
</head>
<body class="container py-4">
<a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    <h2>Manage Organizations</h2>
    <input type="text" id="searchOrgs" class="form-control mb-3" placeholder="Search organizations...">
    <div id="orgsTable"></div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
      <div class="modal-dialog modal-lg"><div class="modal-content">
        <form id="editUserForm">
          <div class="modal-header"><h5>Edit Organization</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <input type="hidden" name="id" id="editUserId">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Full Name</label>
                <input type="text" class="form-control" name="fullname" id="editFullname" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email" id="editEmail" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Address</label>
                <input type="text" class="form-control" name="address" id="editAddress" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Contact</label>
                <input type="text" class="form-control" name="contact" id="editContact" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Birthday</label>
                <input type="date" class="form-control" name="birthday" id="editBirthday">
              </div>
            </div>

            <hr>
            <h6>Verification Documents (Click to Preview)</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Valid ID</label><br>
                <!-- Added a data attribute and a class for easy selection -->
                <img id="editValidId" class="thumb-img preview-trigger" src="" alt="No ID">
              </div>
              <div class="col-md-6 mb-3">
                <label>Selfie</label><br>
                <!-- Added a data attribute and a class for easy selection -->
                <img id="editSelfie" class="thumb-img preview-trigger" src="" alt="No Selfie">
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Save</button>
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div></div>
    </div>
    
    <!-- New Preview Modal for full-size images -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fullImage" src="" class="preview-img">
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
      <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="text-danger">Confirm Delete</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p>Delete <strong id="deleteUserName"></strong>?</p><input type="hidden" id="deleteUserId"></div>
        <div class="modal-footer">
          <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div></div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

let editModal=new bootstrap.Modal(document.getElementById('editUserModal'));
let deleteModal=new bootstrap.Modal(document.getElementById('deleteUserModal'));
let imagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));

function loadOrgs(page=1){
    const search=document.getElementById("searchOrgs").value;
    fetch(`user_list.php?type=organizations&page=${page}&search=${encodeURIComponent(search)}`)
      .then(r=>r.text())
      .then(html=>{document.getElementById("orgsTable").innerHTML=html;});
}

document.addEventListener("DOMContentLoaded",()=>{
    loadOrgs();
    document.getElementById("searchOrgs").addEventListener("input",()=>loadOrgs(1));

    document.getElementById("editUserForm").addEventListener("submit",e=>{
        e.preventDefault();
        const data=new URLSearchParams(new FormData(e.target));
        data.append("action","edit");
        fetch("user_actions.php",{method:"POST",body:data})
          .then(r=>r.json()).then(res=>{
            if(res.success){
              editModal.hide(); 
              loadOrgs(); // FIX: Call loadOrgs() to refresh the table after a successful edit
            }
          });
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click",()=>{
        const id=document.getElementById("deleteUserId").value;
        fetch("user_actions.php",{method:"POST",headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=delete&id=${id}`})
          .then(r=>r.json()).then(res=>{
            if(res.success){deleteModal.hide(); loadOrgs();}
          });
    });
});

function editUser(id){
    fetch("get_user.php?id="+id).then(r=>r.json()).then(u=>{
        if(u){
            document.getElementById("editUserId").value=u.id;
            document.getElementById("editFullname").value=u.fullname;
            document.getElementById("editEmail").value=u.email;
            document.getElementById("editAddress").value=u.address;
            document.getElementById("editContact").value=u.contact;
            document.getElementById("editBirthday").value=u.birthday || "";
            
           
            const validIdPath = '../uploads/ids/'; 
            const selfiePath = '../uploads/selfies/';    

            document.getElementById("editValidId").src = u.valid_id ? validIdPath + u.valid_id : "";
            document.getElementById("editSelfie").src = u.selfie ? selfiePath + u.selfie : "";
            
            editModal.show();
        }
    });
}

function deleteUser(id,name){
    document.getElementById("deleteUserId").value=id;
    document.getElementById("deleteUserName").textContent=name;
    deleteModal.show();
}

// Function to preview an image in a modal
function previewImage(imageSrc) {
    if (imageSrc) {
        document.getElementById("fullImage").src = imageSrc;
        imagePreviewModal.show();
    }
}

// Add event listeners to the thumbnail images for preview
// We use a click listener on the modal itself to handle clicks on the images
document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target.classList.contains('thumb-img')) {
        previewImage(e.target.src);
    }
});
</script>
</body>
</html>
