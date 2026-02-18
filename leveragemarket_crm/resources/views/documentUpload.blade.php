@extends('layouts.crm.crm')

@section('content')
<div class="pc-container mt-0 mb-0 pb-0 pt-0">
  <div class="pc-content mt-0 mb-0 pb-0 pt-0">
    <div class="page-header mb-0 pb-0 mt-0 pt-0">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title h2">
              <h4 class="mb-0">Document Upload</h4>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-0 pb-0 mt-0 pt-0">
      <div class="col-sm-12">
        <div id="basicwizard" class="form-wizard row justify-content-center">
          <div class="col-12">
            <div class="card mb-2 mt-2 pt-0">
              <div class="card-body p-2">
                <ul class="nav nav-pills nav-justified">
                  <li class="nav-item">
                    <a data-bs-toggle="tab" data-bs-target="#contactDetail" role="tab" class="nav-link active">
                      <i class="ph-duotone ph-chat-teardrop-text"></i>
                      <span class="d-none d-sm-inline">Instructions</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a data-bs-toggle="tab" data-bs-target="#jobDetail" role="tab" class="nav-link icon-btn">
                      <i class="ph-duotone ph-user-list"></i>
                      <span class="d-none d-sm-inline">Proof of Identity</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a data-bs-toggle="tab" data-bs-target="#educationDetail" role="tab" class="nav-link icon-btn">
                      <i class="ph-duotone ph-map-pin-line"></i>
                      <span class="d-none d-sm-inline">Proof of Address</span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <form method="post" action="/user/documentUpload" enctype="multipart/form-data" id="documentUploadForm">
                  @csrf
                  <div class="tab-content">

                    {{-- Instructions Tab --}}
                    <div class="tab-pane show active" id="contactDetail">
                      <div class="row mt-3">
                        <div class="col-sm-auto text-center col-lg-4">
                          <img src="/assets/images/doc_upload.png" alt="user-image" class="rounded img-fluid ms-2 mt-3" style="width: 80%;">
                        </div>
                        <div class="col-sm-auto col-lg-8">
                          <div class="text-muted ms-4 mb-4">
                            <h3 class="mb-2">Instructions to Upload Your Documents</h3>
                            <small>Your document security is important to us. All files uploaded through this portal are securely stored.</small>
                          </div>
                          <div class="row">
                            <div class="col-sm-6">
                              <ul class="list-group list-group-flush">
                                <li class="list-group-item">✔ Acceptable formats: PDF, JPG, JPEG, PNG.</li>
                                <li class="list-group-item">✔ Each file size should not exceed 5 MB.</li>
                                <li class="list-group-item">✔ Documents must be clear and legible.</li>
                              </ul>
                            </div>
                            <div class="col-sm-6">
                              <ul class="list-group list-group-flush">
                                <li class="list-group-item">✔ Must be a government-issued identity card.</li>
                                <li class="list-group-item">✔ Document must be NON-EXPIRED.</li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="text-end mt-4">
                        <button type="button" id="nextToJobDetail" class="btn btn-primary" style="min-width: 150px; padding: 12px 24px;">Next</button>
                      </div>
                    </div>

                    {{-- Proof of Identity Tab --}}
                    <div class="tab-pane" id="jobDetail">
                      {{-- <div class="text-start mt-3">
                        <button type="button" id="prevToInstructions" class="btn btn-secondary mb-3">
                          <i class="fa fa-arrow-left me-1"></i> Previous
                        </button>
                      </div> --}}

                      <div class="row">
                        <div class="text-muted">
                          <h3 class="mb-2">Upload Your Proof of Identity</h3>
                          <small class="text-danger">BOTH FILES ARE MANDATORY. If your document includes both the front and back sides in a single file, please upload the same file in both fields.</small>
                        </div>
                        <div class="col-6">
                          <div class="row mt-4">
                            <div class="col-12">
                              <div class="text-muted">
                                <h5 class="mb-1">Front Side</h5>
                                <small>Upload the front side of your ID</small>
                              </div>
                              <input name="image" required type="file" accept="application/pdf,image/png,image/jpeg" class="form-control">
                              <small class="text-muted mt-2">Maximum File Size Allowed: 2MB</small>
                            </div>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="row mt-4">
                            <div class="col-12">
                              <div class="text-muted">
                                <h5 class="mb-1">Back Side</h5>
                                <small>Upload the back side of your ID</small>
                              </div>
                              <input name="image1" required type="file" accept="application/pdf,image/png,image/jpeg" class="form-control">
                              <small class="text-muted mt-2">Maximum File Size Allowed: 2MB</small>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="text-end mt-4">
                      <button type="button" id="nextToEducationDetail" class="btn btn-primary"  style="min-width: 150px; padding: 12px 24px;">
                        Next
                      </button>
                    </div>
                    </div>

                    {{-- Proof of Address Tab --}}
                    <div class="tab-pane" id="educationDetail">
                      <div class="row">
                        <div class="text-muted">
                          <h3 class="mb-2">Upload Your Proof of Address</h3>
                          <small class="text-danger">Please upload a document to verify your Proof of Address.</small>
                        </div>
                        <div class="col-6">
                          <div class="row mt-4">
                            <div class="col-12">
                              <div class="text-muted">
                                <h5 class="mb-1">Attach any one of this documents</h5>
                                <small>(Electricity Bill, Telephone Bill, Bank Statement)</small>
                              </div>
                              <input name="image2" required type="file" accept="application/pdf,image/png,image/jpeg" class="form-control">
                              <small class="text-muted mt-2">Maximum File Size Allowed: 2MB</small>
                            </div>
                          </div>
                        </div>

                        <div class="d-flex wizard justify-content-between mt-4">
                          <div class="first"></div>
                          <div class="d-flex">
                            <div class="next">
                              <button type="submit" class="btn btn-primary mt-3 mt-md-0">Upload Documents</button>
                            </div>
                          </div>
                          <div class="last"></div>
                        </div>
                      </div>
                    </div>

                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Flash Messages --}}
@if (session('success'))
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: '{{ session('success') }}',
    }).then(() => location.reload());
  </script>
@endif

@if (session('error'))
  <script>
    Swal.fire({
      icon: 'warning',
      title: 'Something went wrong',
      text: '{{ session('error') }}',
      showConfirmButton: true
    });
  </script>
@endif

{{-- Tab Navigation Script --}}
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const nextBtnToJob = document.getElementById('nextToJobDetail');
    const prevBtnToInstructions = document.getElementById('prevToInstructions');
    const nextBtnToEducation = document.getElementById('nextToEducationDetail');

    if (nextBtnToJob) {
      nextBtnToJob.addEventListener('click', function () {
        const nextTabTrigger = document.querySelector('[data-bs-target="#jobDetail"]');
        if (nextTabTrigger) {
          const tabInstance = new bootstrap.Tab(nextTabTrigger);
          tabInstance.show();
          setTimeout(() => {
            document.querySelector('#jobDetail')?.scrollIntoView({ behavior: 'smooth' });
          }, 300);
        }
      });
    }

    if (prevBtnToInstructions) {
      prevBtnToInstructions.addEventListener('click', function () {
        const prevTabTrigger = document.querySelector('[data-bs-target="#contactDetail"]');
        if (prevTabTrigger) {
          const tabInstance = new bootstrap.Tab(prevTabTrigger);
          tabInstance.show();
          setTimeout(() => {
            document.querySelector('#contactDetail')?.scrollIntoView({ behavior: 'smooth' });
          }, 300);
        }
      });
    }

    if (nextBtnToEducation) {
      nextBtnToEducation.addEventListener('click', function () {
        const nextTabTrigger = document.querySelector('[data-bs-target="#educationDetail"]');
        if (nextTabTrigger) {
          const tabInstance = new bootstrap.Tab(nextTabTrigger);
          tabInstance.show();
          setTimeout(() => {
            document.querySelector('#educationDetail')?.scrollIntoView({ behavior: 'smooth' });
          }, 300);
        }
      });
    }
  });
</script>
@endsection
