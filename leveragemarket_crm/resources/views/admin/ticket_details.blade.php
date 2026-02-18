@extends('layouts.admin.admin')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
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
           
            font-weight: 700 !important;
            background-color: #a9f3a0;
            font: 500 12px/1 "Lato", sans-serif;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            text-align: center;
        }

        /* Left and Right Chat Bubbles */
  /* ================= CHAT CONTAINER ================= */

.chat-card {
    display: flex;
    flex-direction: column;
    height: 60vh;
    border-radius: 12px;
    overflow: hidden;
}

/* ================= CHAT BODY ================= */

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

/* ================= CHAT BUBBLES ================= */

.chat-bubble {
    max-width: 75%;
    padding: 10px 15px;
    border-radius: 18px;
    margin-bottom: 10px;
    clear: both;
    font-size: 14px;
    word-wrap: break-word;
    line-height: 1.4;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
}

.time {
    font-size: 11px;
    color: #555;
    text-align: right;
    margin-top: 4px;
}

.ticks {
    margin-right: 4px;
}

.ticks.seen {
    color: #0d6efd;
}

.ticks.delivered {
    color: #0c0a0aff;
}
/* ADMIN MESSAGE (LEFT) */
.chat-bubble.received {
    background: #e0e0e0;
    color: #000;
    float: left;
    border-bottom-left-radius: 0;
}

/* USER MESSAGE (RIGHT) */
.chat-bubble.sent {
    background: #e0e0e0;
    color: #000000;
    float: right;
    border-bottom-right-radius: 0;
}

/* ================= MESSAGE TIME ================= */

.chat-bubble .time {
    display: block;
    font-size: 10px;
    margin-top: 5px;
    text-align: right;
    opacity: 0.7;
}

/* ================= ATTACHMENT LINK ================= */

.chat-bubble a {
    color: #ffd966;
    font-size: 12px;
    text-decoration: none;
}

.chat-bubble.sent a {
    color: #333;
}

.chat-bubble a:hover {
    text-decoration: underline;
}

/* ================= INPUT AREA ================= */

.card-footer {
    background: #f5f5f5;
    border-top: 1px solid #ddd;
}

.chat-input {
    display: flex;
    gap: 6px;
    align-items: center;
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
.chat-input textarea:focus {
    outline: none;
    border-color: #198754;
}




/* ================= FILE DISPLAY ================= */

#file-display {
    font-size: 12px;
    margin-bottom: 4px;
    color: #333;
}

/* ================= BUTTONS ================= */

.chat-input .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

/* ================= SCROLLBAR ================= */

.msg_card_body::-webkit-scrollbar {
    width: 6px;
}

.msg_card_body::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.3);
    border-radius: 10px;
}



/* ================= CHAT ATTACHMENTS ================= */

.chat-attachment {
    margin-top: 8px;
}

/* IMAGE STYLE */
.chat-attachment.image img {
    max-width: 220px;
    border-radius: 12px;
    display: block;
    cursor: default;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* PDF STYLE */
.chat-attachment.pdf embed {
    width: 220px;
    height: 200px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.chat-attachment.pdf a {
    display: inline-block;
    margin-top: 5px;
    font-size: 12px;
    color: #0d6efd;
    text-decoration: none;
}

.chat-attachment.pdf a:hover {
    text-decoration: underline;
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




    </style>
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentModalLabel">Attachment Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed id="attachmentFile" src="" type="" width="100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="ticketReassignModal">
        <form method="post" action="{{ route('admin.assignTicket') }}">
            @csrf

            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Reassign #TICKET-{{ sprintf('%02d', $ticket->ticket_id + 10000) }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="ticket_id" value="{{ $ticket->ticket_id }}">

                        <select class="form-control" name="assignee" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->client_index }}">
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reassign</button>
                    </div>

                </div>
            </div>

        </form>
    </div>


    <div class="modal" tabindex="-1" id="ticketStatusUpdateModal">
        <form method="post" action="{{route('admin.updateStatus')}}">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <input type="hidden" name="status_update" value="true">
                            <input type="hidden" name="ticket_id" value="{{ request()->query('id') }}">
                            <select class="form-control" name="ticket_status_id" required>
                                @foreach ($ticket_status as $status)
                                    <option value="{{ $status['id'] }}" {{ $status['ticket_status'] == $ticket->ticket_status ? 'selected' : '' }}>
                                        {{ $status['ticket_status'] }}
                                    </option>
                                @endforeach
                            </select>

                             
                            <textarea
            class="form-control mt-4"
            name="remarks"
            rows="4"
            placeholder="Enter status update description..."
            required
        ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-primary" value="Update">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Ticket Details</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket Details</li>
                </ol>
            </div>

            <div class="row">
                <!-- Left column: Ticket Details -->
                <div class="col-md-6">

                    <div class="card custom-card shadow-sm ribbon-card mb-3 position-relative">
                        <div class="p-2 gap-2 d-flex justify-content-end">
                            <a href="#" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#ticketReassignModal">Reassign</a>
                            <a href="#" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#ticketStatusUpdateModal">Update Status</a>
                        </div>
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
                                        <i data-feather="calendar" class="mb-1 text-primary" style="font-size: 1.5rem;"></i>
                                        <p class="mb-0 small text-muted">Created At</p>
                                        <h6 class="fw-bold text-size mt-2">{{ $ticket->created_at }}</h6>
                                    </div>
                                </div>


                                <!-- Account Number -->
                                <div class="col-4 ">
                                    <div
                                        class="card shadow-sm pt-4 px-1 h-100 animate-card d-flex flex-column align-items-center">
                                        <i data-feather="user" class="mb-1 text-info"></i>
                                        <p class="mb-0 small text-muted">Account Number</p>
                                        <h6 class="fw-bold text-size mt-2">{{ !empty($ticket->live_account) ? $ticket->live_account : 'Not Live_Account'}}</h6>
                                    </div>
                                </div>

                                <!-- Assigned To -->
                                <div class="col-4">
                                    <div
                                        class="card shadow-sm pt-4 px-1 h-100 animate-card d-flex flex-column align-items-center">
                                        <i data-feather="user-check" class="mb-1 text-success"></i>
                                        <p class="mb-0 small text-muted">Assigned To</p>
                                        <h6 class="fw-bold">
                                            {{ !empty($ticket->assignee) ? $ticket->assignee : 'Assign Follow-Up' }}
                                        </h6>

                                    </div>
                                </div>
                                <div class="col-12">
                                    <div
                                        class="card shadow-sm pt-4 px-3 h-100 animate-card text-center d-flex flex-column align-items-center">
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
                                    <span class="mb-1 fw-semibold"> Last Follow-Up By</span>
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

                                <!-- Attachment Button -->
                                <label for="file-upload"
                                    class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; cursor:pointer;">
                                   <i class="fa-solid fa-paperclip attach-icon"></i>
                                </label>

                                <!-- Hidden File Input -->
                                <input type="file" id="file-upload" name="attachment" accept=".jpeg,.png,.jpg,.pdf"
                                    class="d-none" onchange="showSelectedFile()">

                                <!-- Message Box -->
                               <textarea placeholder="Type your message here..." name="remark" ></textarea>

                                <!-- Send -->
                               <button type="submit"  class="btn btn-primary border rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px; cursor:pointer;">
    <i class="fa-solid fa-paper-plane"></i> 
</button>

                                <input type="hidden" name="add_remark" value="true">
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
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

    $.get("/admin/ticket_followups?id=<?php echo $_GET['id']; ?>", function (data) {

        let container = $("#followup_logs");
        container.html('');

        data.forEach(function (msg) {

            /* 🔁 ADMIN LOGIC
               admin = RIGHT
               user  = LEFT
            */
          let bubbleClass = msg.user_type === 'admin' ? 'sent' : 'received';
          let ticks = '';
if (msg.user_type === 'admin') {
    ticks = msg.is_seen
        ? '<span class="ticks seen">✔✔</span>'
        : '<span class="ticks delivered">✔</span>';
}

let attachmentHtml = '';

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
                <a href="${msg.attachment}" target="_blank"><i class="fa-solid fa-eye attach-icon" title="View Attachment"></i> PDF</a>
            </div>
        `;
    }
}

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

       container.scrollTop(container[0].scrollHeight);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll('.animate-btn');
    buttons.forEach((btn, index) => {
        btn.style.animationDelay = `${index * 0.2}s`;
    });
});
</script>


  
@endsection