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
    <title>Manage Donors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="managedonors.css"/>
</head>
<main class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Donors</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-back-modern">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            <span class="d-none d-md-inline ms-2">Back to Dashboard</span>
        </a>
    </div>

    <div class="input-group mb-4">
        <span class="input-group-text">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
            </svg>
        </span>
        <input type="text" id="searchDonors" class="form-control" placeholder="Search donors...">
    </div>

    <div id="donorsTable"></div>

    <section>
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="editUserForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit Donor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="editUserId">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="editFullname" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="fullname" id="editFullname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" id="editAddress" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editContact" class="form-label">Contact</label>
                                    <input type="text" class="form-control" name="contact" id="editContact" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="editBirthday" class="form-label">Birthday</label>
                                    <input type="date" class="form-control" name="birthday" id="editBirthday">
                                </div>
                            </div>
                            <hr class="my-4">
                            <h6>Verification Documents <small class="text-muted">(Click to Preview)</small></h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Valid ID</label>
                                    <img id="editValidId" class="thumb-img img-fluid rounded border cursor-pointer" src="" alt="No ID">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Selfie</label>
                                    <img id="editSelfie" class="thumb-img img-fluid rounded border cursor-pointer" src="" alt="No Selfie">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">Save Changes</button>
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="fullImage" src="" class="preview-img img-fluid">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteUserModalLabel">Confirm Deletion</h5>
                        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to permanently delete this donor's account: <strong id="deleteUserName"></strong>?</p>
                        <input type="hidden" id="deleteUserId">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
    
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

function loadDonors(page=1){
    const search=document.getElementById("searchDonors").value;
    fetch(`user_list.php?type=donors&page=${page}&search=${encodeURIComponent(search)}`)
      .then(r=>r.text())
      .then(html=>{document.getElementById("donorsTable").innerHTML=html;});
}

document.addEventListener("DOMContentLoaded",()=>{
    loadDonors();
    document.getElementById("searchDonors").addEventListener("input",()=>loadDonors(1));

    document.getElementById("editUserForm").addEventListener("submit",e=>{
        e.preventDefault();
        const data=new URLSearchParams(new FormData(e.target));
        data.append("action","edit");
        fetch("user_actions.php",{method:"POST",body:data})
          .then(r=>r.json()).then(res=>{
            if(res.success){
                editModal.hide(); 
                loadDonors();
            }
          });
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click",()=>{
        const id=document.getElementById("deleteUserId").value;
        fetch("user_actions.php",{method:"POST",headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=delete&id=${id}`})
          .then(r=>r.json()).then(res=>{
            if(res.success){deleteModal.hide(); loadDonors();}
          });
    });
    
    // Add event listener to handle thumbnail clicks in the edit modal
    document.getElementById('editUserModal').addEventListener('click', function(e) {
        if (e.target.classList.contains('thumb-img')) {
            previewImage(e.target.src);
        }
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
            
            // We prepend the correct server paths to the image filenames
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
    if (imageSrc && imageSrc.length > 0) {
        document.getElementById("fullImage").src = imageSrc;
        imagePreviewModal.show();
    }
}
</script>
</body>
</html>
