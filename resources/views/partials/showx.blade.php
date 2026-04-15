<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<link rel="stylesheet" href="/assets/beta/popup/style.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />

<!-- showx.blade.php -->


@if(session()->has('showx'))
    @foreach(session('showx') as $msg)
        @if($msg[0] === 'error')
            <!-- Error Popup -->
            <section class="active" id="errorPopup">
                <div class="modal-box">
                    <i class="fas fa-exclamation-circle"></i>
                    <h2>Error</h2>
                    <h3>{{ __($msg[1]) }}</h3>
                    <div class="buttons">
                        <button class="close-btn">Ok, Close</button>
                    </div>
                </div>
            </section>
        @elseif($msg[0] === 'success')
            <!-- Success Popup -->
            <section class="active" id="successPopup">
                <span class="overlay"></span>
                <div class="modal-box">
                    <i class="fas fa-check-circle"></i>
                    <h2>Success</h2>
                    <h3>{{ __($msg[1]) }}</h3>
                    <div class="buttons">
                        <button class="close-btn">Ok, Close</button>
                    </div>
                </div>
            </section>
        @elseif($msg[0] === 'trxsuccess')
            <!-- Success Popup -->
            <section class="active" id="successPopup">
                <span class="overlay"></span>
                <div class="modal-box">
                    <i class="fas fa-check-circle"></i>
                    <h2>Success</h2>
                    <h3>{{ __($msg[1]) }}</h3>
                    <div class="buttons">
                        <button class="close-btn">Close</button>
                        <button>Reciept</button>
                    </div>
                </div>
            </section>
        @elseif($msg[0] === 'info')
            <!-- Info Popup -->
            <section class="active" id="infoPopup">
                <span class="overlay"></span>
                <div class="modal-box">
                    <i class="fas fa-info-circle"></i>
                    <h2>Info</h2>
                    <h3>{{ __($msg[1]) }}</h3>
                    <div class="buttons">
                        <button class="close-btn">Ok, Close</button>
                    </div>
                </div>
            </section>
        @elseif($msg[0] === 'warning')
            <!-- Warning Popup -->
            <section class="active" id="warningPopup">
                <span class="overlay"></span>
                <div class="modal-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h2>Warning</h2>
                    <h3>{{ __($msg[1]) }}</h3>
                    <div class="buttons">
                        <button class="close-btn">Ok, Close</button>
                    </div>
                </div>
            </section>
        @elseif($msg[0] === 'processing')
            <!-- Processing Popup -->
            <section class="active" id="processingPopup">
                <span class="overlay"></span>
                <div class="modal-box processing">
                    <div class="processing-icon"></div>
                    <h2>Processing...</h2>
                </div>
            </section>
        @elseif($msg[0] === 'loading')
            <!-- Loading Popup -->
            <section class="active" id="loadingPopup">
                <span class="overlay"></span>
                <div class="modal-box loading">
                    <div class="spinner"></div>
                    <h2>Loading...</h2>
                </div>
            </section>    
        @endif
    @endforeach
@endif

<!-- Add this inside your showx.blade.php file after the existing script tag -->
<script>
      const section = document.querySelector("section"),
        overlay = document.querySelector(".overlay"),
        showBtn = document.querySelector(".show-modal"),
        closeBtn = document.querySelector(".close-btn");

      showBtn.addEventListener("click", () => section.classList.add("active"));

      overlay.addEventListener("click", () =>
        section.classList.remove("active")
      );

      closeBtn.addEventListener("click", () =>
        section.classList.remove("active")
      );
    </script>
