@extends('layouts.crm.crm')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* top left */
.ribbon-top-left {
    top: -7px;
    inset-inline-start: -7px;
}

.ribbon-top-left::before,
.ribbon-top-left::after {
    border-top-color: transparent;
    border-left-color: transparent;
}

.ribbon-top-left::before {
    top: 0;
    inset-inline-end: 0;
    z-index: 1;
}

.ribbon-top-left::after {
    bottom: 0;
    inset-inline-start: 0;
    z-index: 1;
}

.ribbon-top-left span {
    inset-inline-end: -12px;
    top: 20px;
    transform: rotate(-45deg);
}

[dir=rtl] .ribbon-top-left span {
    transform: rotate(45deg);
}

/* top left */
        .ribbon-top-left {
            top: -7px;
            inset-inline-start: -7px;
        }

        .ribbon {
            width: 80px;
            height: 80px;
            overflow: hidden;
            position: absolute;
            z-index: 1;
        }

      .ribbon span {
            position: absolute;
            display: block;
            width: 120px;
            padding: 6px 0;
            z-index: 2;
            color: #0e0e0eff;
            background-color: #a9f3a0;
           font-weight: 700 !important;
            font: 500 12px/1 "Lato", sans-serif;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            text-align: center;
        }

        /* Left and Right Chat Bubbles */
        .chat-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 20px;
            margin-bottom: 10px;
            position: relative;
            word-wrap: break-word;
        }

        .chat-bubble.sent {
            background-color: #dcf8c6;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .chat-bubble.received {
            background-color: #fff;
            border: 2px solid #ececec;
            margin-right: auto;
            border-bottom-left-radius: 0;
        }

        .chat-bubble .time {
            display: block;
            font-size: 10px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        .chat-card {
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

            .msg_card_body {
    flex: 1;
    overflow-y: auto;
    padding: 15px;

    background-image: 
        linear-gradient(rgba(18, 163, 0, 0.35), rgba(18, 163, 0, 0.35)),
        url('{{ asset('assets/images/chat-bg.jpg') }}');
  
    background-size: cover;
    background-position: center;
}

        .chat-input {
            display: flex;
            align-items: center;
            gap: 5px;
            padding-top: 5px;
        }

       
        .chat-input textarea {
    flex: 1;
    resize: none;
    border-radius: 20px;
    padding: 0px 13px;
    border: 1px solid #ccc;
    font-size: 14px;
    justify-content: center;

}
/* Optional: placeholder style */
.chat-input textarea::placeholder {
    justify-content: center;      /* center placeholder text */
    color: #999;             /* lighter color */
    opacity: 1;              /* make it fully visible */
}

        #file-display {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .rotate-img {
            display: block;
            justify-content: center;
            /* ensures inline spacing doesn’t affect layout */
            margin: auto;
            /* center within container */
            animation: rotateLogo 3s linear infinite;
            transform-origin: center center;
            /* rotation around center */
            width: 100px;
            /* fixed width */
            height: 100px;
            /* fixed height */
        }
/* ================= CHAT BUBBLES ================= */

.chat-bubble {
    max-width: 75%;
    padding: 10px 14px;
    border-radius: 16px;
    margin-bottom: 10px;
    clear: both;
    font-size: 14px;
    word-wrap: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

/* USER (RIGHT) */
.chat-bubble.sent {
    background: #dcf8c6;
    float: right;
    border-bottom-right-radius: 4px;
}

/* ADMIN (LEFT) */
.chat-bubble.received {
    background: #ffffff;
    float: left;
    border-bottom-left-radius: 4px;
}

/* MESSAGE TEXT */
.msg-text {
    margin-bottom: 6px;
}

/* TIME */
.chat-bubble .time {
    font-size: 10px;
    text-align: right;
    opacity: 0.6;
}

/* ================= ATTACHMENTS ================= */

.chat-attachment {
    margin-top: 6px;
}

/* IMAGE */
.chat-attachment.image img {
    max-width: 220px;
    border-radius: 10px;
    display: block;
    cursor: default;
}

/* PDF */
.chat-attachment.pdf embed {
    width: 220px;
    height: 180px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.chat-attachment.pdf a {
    display: inline-block;
    margin-top: 4px;
    font-size: 12px;
    color: #0d6efd;
    text-decoration: none;
}

        @keyframes rotateLogo {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .btn-hover-animate {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-hover-animate::after {
            content: "";
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-hover-animate:hover::after {
            left: 0;
        }

        .btn-hover-animate:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Card entrance animation */
        @keyframes slideUpFade {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }



        .animate-card {
            opacity: 0;
            animation: slideUpFade 0.5s forwards;
            animation-delay: 0.2s;
        }

        .animate-btn {
            opacity: 0;
            animation: slideUpFade 0.5s forwards;
        }

        .animate-btn:nth-child(1) {
            animation-delay: 0.2s;
        }

        .animate-btn:nth-child(2) {
            animation-delay: 0.4s;
        }

        .animate-btn:nth-child(3) {
            animation-delay: 0.6s;
        }
        .text-size {
            font-size: 13px;
        }

        .tic-badge {
            background: #a9f3a0;
            border-radius: 25px;
            padding: 10px;
            max-width: 142px;
            margin: auto;
        }

        .chat-bubble.sent {
    background: #e0e0e0;
    color: #000;
    padding: 10px;
    border-radius: 20px;
    float: right;
    clear: both;
    margin-bottom: 10px;
    max-width: 75%;
}
.chat-bubble.received {
    background: #e0e0e0;
    color: #000;
    padding: 10px;
    border-radius: 20px;
    float: left;
    clear: both;
    margin-bottom: 10px;
    max-width: 75%;
}
    </style>

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-3 pb-0">
                <div class="page-block">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-12">
                            <h4 class="mb-0">Ticket Details</h4>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left column: Ticket Details -->
                        <div class="col-md-6">
                            <div class="card custom-card shadow-sm ribbon-card mb-3 position-relative">
                                <!-- Ribbon -->
                                <div class="ribbon ribbon-{{ $ticket->ticket_label }} ribbon-top-left">
                                    <span class=" bg-{{ $ticket->ticket_label }}">{{ $ticket->ticket_status }}</span>
                                </div>

                                <div class="card-body p-4">
                                    <!-- Live Account Icon -->
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('assets/images/mt5.png') }}" alt="account-icon"
                                            class="rounded-circle shadow-sm rotate-img"
                                            style="width:80px; height:80px; object-fit:cover;">
                                    </div>

                                    <!-- Integration Name & Ticket Number -->
                                    <div class="text-center mb-3">
                                        <h5 class="mb-1 fw-bold">{{ $ticket->subject_name }}</h5>
                                         <h6 class="tic-badge fw-bold text-white">TICKET-{{ sprintf('%02d', $ticket->ticket_id + 10000) }}
                                </h6>
                                    </div>

                                    <!-- Ticket Info Cards -->
                                    <div class="row g-3 text-center mb-4">
                                        <!-- Created At -->
                                        <div class="col-4 d-flex justify-content-center">
                                            <div
                                                class="card shadow-sm pt-4 px-1 h-100 animate-card d-flex flex-column align-items-center">
                                                <i data-feather="calendar" class="mb-1 text-primary"
                                                    style="font-size: 1.5rem;"></i>
                                                <p class="mb-0 small text-muted">Created At</p>
                                                <h6 class="fw-bold text-size mt-2">{{ $ticket->created_at }}</h6>
                                            </div>
                                        </div>


                                        <!-- Account Number -->
                                        <div class="col-4 ">
                                            <div class="card shadow-sm pt-4 px-1 h-100 animate-card d-flex flex-column align-items-center">
                                                <i data-feather="user" class="mb-1 text-info"></i>
                                                <p class="mb-0 small text-muted">Account Number</p>
                                                <h6 class="fw-bold text-size mt-2">{{ !empty($ticket->live_account) ? $ticket->live_account : 'Not Live_Account'}}</h6>
                                            </div>
                                        </div>

                                        <!-- Assigned To -->
                                        <div class="col-4">
                                            <div class="card shadow-sm pt-4 px-1 h-100 animate-card d-flex flex-column align-items-center">
                                                <i data-feather="user-check" class="mb-1 text-success"></i>
                                                <p class="mb-0 small text-muted">Assigned To</p>
                                                <h6 class="fw-bold ">{{ !empty($ticket->assignee) ? $ticket->assignee : 'Assign Follow-Up' }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card shadow-sm pt-4 px-3 h-100 animate-card text-center d-flex flex-column align-items-center">
                                                <i data-feather="file-text" class="mb-2 text-primary "
                                                    style="font-size: 1.5rem;"></i>
                                                <p class="mb-1 small text-muted">Description</p>
                                                <h6 class="fw-semibold">{{ $ticket->discription }}</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Key Data Buttons / Pills -->
                                    <div class="d-flex justify-content-around flex-wrap gap-3 mt-3">
                                        <div class="d-flex flex-column align-items-center animate-btn">
                                            <span class="mb-1 fw-semibold">Ticket Type</span>
                                            <button class="btn btn-outline-primary btn-hover-animate">
                                                {{ $ticket->ticket_type }}
                                            </button>
                                        </div>
                                        <div class="d-flex flex-column align-items-center animate-btn">
                                            <span class="mb-1 fw-semibold">Last Follow-Up</span>
                                            <button class="btn btn-outline-info btn-hover-animate">
                                            {{ !empty($ticket->last_followup) ? $ticket->last_followup : $ticket->created_at}}
                                            </button>
                                        </div>
                                        <div class="d-flex flex-column align-items-center animate-btn">
                                            <span class="mb-1 fw-semibold">Follow-Up By</span>
                                            <button class="btn btn-outline-success btn-hover-animate">
                                  {{ !empty($ticket->last_assign_up) ? $ticket->last_assign_up : 'No Follow-Up' }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>



                        <!-- Right column: WhatsApp style Chat -->
                        <div class="col-md-6">
                            <div class="card chat-card" id="chat-card">
                               <div id="followup_logs" class="msg_card_body">
    <!-- Messages will load here -->
</div>

                                <div class="card-footer">
                                    <div id="file-display"></div>
                                    <form class="chat-input" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="input-group-text attach_btn" for="file-upload">
                                            <i data-feather="paperclip"></i>
                                        </label>
                                        <input type="file" accept=".jpeg,.png,.jpg,.pdf" id="file-upload" name="attachment"
                                            class="d-none" onchange="showSelectedFile()">
                                        <textarea placeholder="Type a message ......." name="remark" required></textarea>
                                      <button type="submit" class="btn btn-primary">
    <i class="fa-solid fa-paper-plane"></i> 
</button>

                                        <input type="hidden" name="add_remark" value="true">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> <!-- End row -->
                </div>
            </div>
        </div>
    </div>

    <!-- Attachment Modal -->
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentModalLabel">Attachment Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <embed id="attachmentFile" src="" type="" width="100%">
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: '{{ session('error') }}'
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            loadFollowups();
            setInterval(loadFollowups, 3000);
        });

        function showSelectedFile() {
            const fileInput = document.getElementById('file-upload');
            const fileDisplay = document.getElementById('file-display');
            if (fileInput.files.length > 0) {
                fileDisplay.innerHTML = '<strong>Selected file:</strong> ' + fileInput.files[0].name;
            } else {
                fileDisplay.innerHTML = '';
            }
        }

        $('#attachmentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var fileSrc = button.data('bs-file');
            var fileType = button.data('bs-type');
            var modal = $(this);
            modal.find('#attachmentFile').attr('src', fileSrc);
            modal.find('#attachmentFile').attr('type', fileType);
        });
function loadFollowups() {

    $.get("/ticket_followups?id=<?php echo $_GET['id']; ?>", function (data) {

        let container = $("#followup_logs");
        container.html('');

        data.forEach(function (msg) {

         // Determine bubble side: user = right, admin = left
let bubbleClass = msg.user_type === 'user' ? 'sent' : 'received';

let attachmentHtml = '';
let ticks = '';
if (msg.user_type === 'user') {
    ticks = msg.is_seen
        ? '<span class="ticks seen">✔✔</span>'
        : '<span class="ticks delivered">✔</span>';
}
if (msg.attachment) {
    let ext = msg.attachment.split('.').pop().toLowerCase();

    // IMAGE PREVIEW
    if (['jpg', 'jpeg', 'png'].includes(ext)) {
        attachmentHtml = `
            <div class="chat-attachment image">
                <img src="${msg.attachment}" alt="chat image">
            </div>
        `;
    }

    // PDF PREVIEW
    else if (ext === 'pdf') {
        attachmentHtml = `
            <div class="chat-attachment pdf">
                <embed src="${msg.attachment}" type="application/pdf">
                <a href="${msg.attachment}" target="_blank">View PDF</a>
            </div>
        `;
    }
}

// Chat bubble HTML
let bubble = `
    <div class="chat-bubble ${bubbleClass}">
        ${msg.remarks ? `<div class="msg-text">${msg.remarks}</div>` : ''}
        ${attachmentHtml}
        <div class="time">
            ${new Date(msg.added_at).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        })} ${ticks}
        </div>
    </div>
`;

container.append(bubble);

        });

        // Scroll to bottom
        container.scrollTop(container[0].scrollHeight);
    });
}



        document.addEventListener("DOMContentLoaded", function () {
            // Add staggered animation delays
            const buttons = document.querySelectorAll('.animate-btn');
            buttons.forEach((btn, index) => {
                btn.style.animationDelay = `${index * 0.2}s`;
            });
        });
    </script>
@endsection