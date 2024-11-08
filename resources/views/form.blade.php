<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery Validation Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>

    <!-- jQuery Validation Additional Methods -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
</head>

<body>

    <div class="container mt-5">
        <!-- Button to open modal -->
        <button class="btn btn-primary mb-3 float-end" data-bs-toggle="modal" data-bs-target="#userModal">Create User</button>

        <!-- Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Create User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form inside the modal -->
                        <form id="userForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                <p class="text-danger" id="email-error"></p>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Role</label>
                                <select class="form-select" id="role_id" name="role_id">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="profile" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profile" name="profile">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table to display user data -->
        <h3 class="mt-5">User Data</h3>
        <table class="table table-striped mt-3" id="userTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Profile Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->role ? $user->role->name : 'N/A' }}</td>
                    <td>
                        @if($user->profile)
                        <img src="{{$user->profile }}" alt="Profile Image" width="50">
                        @else
                        N/A
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Add custom regex method to jQuery Validation plugin
            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please enter a valid Indian phone number.");

            // Initialize the jQuery Validation plugin on the form
            $('#userForm').validate({
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        minlength: 10,
                        maxlength: 15,
                        regex: /^(\+91[\-\s]?)?\(?\d{3}\)?[\-\s]?\d{3}[\-\s]?\d{4}$/
                    },
                    profile: {
                        required: true, // Only "required" validation
                    }
                },
                messages: {
                    name: {
                        required: "Please enter your name."
                    },
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address."
                    },
                    phone: {
                        required: "Please enter your phone number.",
                        minlength: "Phone number must be at least 10 digits.",
                        maxlength: "Phone number cannot exceed 15 digits.",
                        regex: "Please enter a valid Indian phone number."
                    },
                    profile: {
                        required: "Please upload a profile image.",
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // AJAX form submission for creating a user
            $('#submitBtn').on('click', function(e) {
                e.preventDefault();

                if ($('#userForm').valid()) {
                    var formData = new FormData($('#userForm')[0]);

                    $.ajax({
                        url: "{{url('api/users')}}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#userModal').modal('hide');
                            $('#email-error').text('');
                            $('#userTable tbody').append(`
                                <tr>
                                    <td>${response.name}</td>
                                    <td>${response.email}</td>
                                    <td>${response.phone}</td>
                                    <td>${response.role.name}</td>
                                    <td><img src="${response.profile}" width="50"></td>
                                </tr>
                            `);

                            $('#userForm')[0].reset();
                        },
                        error: function (xhr, status, error) {
                            var response = JSON.parse(xhr.responseText);

                            if (response.errors && response.errors.email) {
                                $('#email-error').text(response.errors.email[0]);
                            } else {
                                $('#email-error').text('');
                            }
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>
