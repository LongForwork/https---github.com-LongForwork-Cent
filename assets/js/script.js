/**
 * Cent Beauty - Booking System
 * Main JavaScript file
 */
document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements
  const elements = {
    // Form elements
    bookingForm: document.getElementById("booking-form"),
    branchRadios: document.querySelectorAll(".branch-radio"),
    step1: document.getElementById("step-1"),
    step2: document.getElementById("step-2"),
    step3: document.getElementById("step-3"),
    continueStep1Button: document.getElementById("continue-step-1"),
    continueStep2Button: document.getElementById("continue-step-2"),
    servicesContainer: document.getElementById("services-container"),
    dateList: document.getElementById("date-list"),
    timeSelection: document.getElementById("time-selection"),
    timeList: document.getElementById("time-list"),
    bookButton: document.getElementById("book-button"),

    // Hidden inputs
    userIdInput: document.getElementById("user_id"),
    serviceIdInput: document.getElementById("service_id"),
    bookingDateInput: document.getElementById("booking_date"),
    bookingTimeInput: document.getElementById("booking_time"),

    // Login dialog
    loginDialog: document.getElementById("login-dialog"),
    closeDialogBtn: document.getElementById("close-dialog"),
    loginButton: document.getElementById("login-button"),
    userEmailInput: document.getElementById("user-email"),
    userNameInput: document.getElementById("user-name"),

    // Verification elements
    emailStep: document.getElementById("email-step"),
    verificationStep: document.getElementById("verification-step"),
    verificationCodeInput: document.getElementById("verification-code"),
    verifyButton: document.getElementById("verify-button"),
    resendCodeBtn: document.getElementById("resend-code"),

    // Loading overlay
    loadingOverlay: document.getElementById("loading-overlay"),
  };

  // State
  const state = {
    selectedBranchId: null,
    selectedServiceId: null,
    selectedDate: null,
    selectedTime: null,
    userId: elements.userIdInput.value || null,
    currentEmail: "",
  };

  // Initialize
  init();

  /**
   * Initialize the application
   */
  function init() {
    // Generate date options
    generateDateOptions();

    // Add event listeners
    addEventListeners();
  }

  /**
   * Add event listeners to elements
   */
  function addEventListeners() {
    // Branch selection
    elements.branchRadios.forEach((radio) => {
      radio.addEventListener("change", handleBranchSelection);
    });

    // Continue buttons
    elements.continueStep1Button.addEventListener("click", handleContinueStep1);
    elements.continueStep2Button.addEventListener("click", handleContinueStep2);

    // Login dialog
    elements.closeDialogBtn.addEventListener("click", closeLoginDialog);
    elements.loginButton.addEventListener("click", handleLogin);
    elements.verifyButton.addEventListener("click", handleVerifyOTP);
    elements.resendCodeBtn.addEventListener("click", handleResendOTP);
  }

  /**
   * Handle branch selection
   */
  function handleBranchSelection() {
    state.selectedBranchId = this.value;
    elements.continueStep1Button.disabled = false;
  }

  /**
   * Handle continue button in step 1
   */
  function handleContinueStep1() {
    if (!state.userId) {
      // User is not logged in, show login dialog
      showLoginDialog();
    } else {
      // User is logged in, proceed to step 2
      loadServices(state.selectedBranchId);
      elements.step1.classList.add("hidden");
      elements.step2.classList.remove("hidden");
    }
  }

  /**
   * Handle continue button in step 2
   */
  function handleContinueStep2() {
    elements.step2.classList.add("hidden");
    elements.step3.classList.remove("hidden");
  }

  /**
   * Show login dialog
   */
  function showLoginDialog() {
    elements.loginDialog.classList.remove("hidden");
  }

  /**
   * Close login dialog
   */
  function closeLoginDialog() {
    elements.loginDialog.classList.add("hidden");

    // Reset dialog state
    elements.emailStep.classList.remove("hidden");
    elements.verificationStep.classList.add("hidden");
    elements.userEmailInput.value = "";
    elements.userNameInput.value = "";
    elements.verificationCodeInput.value = "";
  }

  /**
   * Handle login button click
   */
  function handleLogin() {
    const email = elements.userEmailInput.value.trim();

    if (email) {
      if (!validateEmail(email)) {
        showAlert("Vui lòng nhập email hợp lệ");
        return;
      }

      state.currentEmail = email;
      checkUserExists(email);
    } else {
      showAlert("Vui lòng nhập email");
    }
  }

  /**
   * Handle verify OTP button click
   */
  function handleVerifyOTP() {
    const code = elements.verificationCodeInput.value.trim();

    if (code && code.length === 6) {
      verifyOTP(code, state.currentEmail);
    } else {
      showAlert("Vui lòng nhập mã xác nhận 6 số");
    }
  }

  /**
   * Handle resend OTP link click
   * @param {Event} e - Click event
   */
  function handleResendOTP(e) {
    e.preventDefault();
    sendOTP(state.currentEmail);
  }

  /**
   * Check if user exists
   * @param {string} email - User email
   */
  function checkUserExists(email) {
    showLoading();

    fetch("api/check_user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        hideLoading();

        if (data.error) {
          showAlert(data.error);
          return;
        }

        if (data.exists) {
          // User exists, proceed with login
          state.userId = data.user.id;
          elements.userIdInput.value = state.userId;
          elements.loginDialog.classList.add("hidden");

          // Load services for selected branch
          loadServices(state.selectedBranchId);
          elements.step1.classList.add("hidden");
          elements.step2.classList.remove("hidden");
        } else {
          // User doesn't exist, send OTP for registration
          sendOTP(email);
        }
      })
      .catch((error) => {
        hideLoading();
        console.error("Error:", error);
        showAlert("Đã xảy ra lỗi khi kiểm tra tài khoản. Vui lòng thử lại.");
      });
  }

  /**
   * Send OTP to email
   * @param {string} email - User email
   */
  function sendOTP(email) {
    showLoading();

    fetch("api/send_otp.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: email,
      }),
    })
      .then((response) => {
        if (!response.ok) {
          return response.text().then((text) => {
            throw new Error(text);
          });
        }
        return response.json();
      })
      .then((data) => {
        hideLoading();

        if (data.error) {
          showAlert(data.error);
          return;
        }

        // Show verification step
        elements.emailStep.classList.add("hidden");
        elements.verificationStep.classList.remove("hidden");

        // For development purposes, show the code
        // if (data.debug_code) {
        //   console.log("Mã xác nhận:", data.debug_code)
        //   showAlert("Mã xác nhận của bạn là: " + data.debug_code)
        // }
      })
      .catch((error) => {
        hideLoading();
        console.error("Error:", error);
        showAlert("Đã xảy ra lỗi khi gửi mã xác nhận. Vui lòng thử lại.");
      });
  }

  /**
   * Verify OTP
   * @param {string} code - OTP code
   * @param {string} email - User email
   */
  function verifyOTP(code, email) {
    showLoading();

    fetch("api/verify_otp.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        otp_code: code,
        email: email,
        name: elements.userNameInput.value.trim(),
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        hideLoading();

        if (data.error) {
          showAlert(data.error);
          return;
        }

        state.userId = data.id;
        elements.userIdInput.value = state.userId;
        elements.loginDialog.classList.add("hidden");

        // Reset dialog state
        elements.emailStep.classList.remove("hidden");
        elements.verificationStep.classList.add("hidden");

        // Load services for selected branch
        loadServices(state.selectedBranchId);
        elements.step1.classList.add("hidden");
        elements.step2.classList.remove("hidden");
      })
      .catch((error) => {
        hideLoading();
        console.error("Error:", error);
        showAlert("Đã xảy ra lỗi khi xác nhận mã. Vui lòng thử lại.");
      });
  }

  /**
   * Load services for a branch
   * @param {string} branchId - Branch ID
   */
  function loadServices(branchId) {
    showLoading();

    fetch(`api/get_services.php?branch_id=${branchId}`)
      .then((response) => response.json())
      .then((services) => {
        hideLoading();

        if (services.error) {
          showAlert(services.error);
          return;
        }

        renderServices(services);
      })
      .catch((error) => {
        hideLoading();
        console.error("Error:", error);
        showAlert("Đã xảy ra lỗi khi tải dịch vụ. Vui lòng thử lại.");
      });
  }

  /**
   * Render services in the services container
   * @param {Array} services - List of services
   */
  function renderServices(services) {
    elements.servicesContainer.innerHTML = "";

    services.forEach((service) => {
      const serviceItem = document.createElement("div");
      serviceItem.className = "service-item";
      serviceItem.innerHTML = `
        <input type="radio" name="service_id" id="service-${
          service.id
        }" value="${service.id}" class="service-radio">
        <div class="service-details">
            <label for="service-${service.id}" class="service-name">${
        service.name
      }</label>
            ${
              service.description
                ? `<p class="service-description">${service.description}</p>`
                : ""
            }
            <div class="service-meta">
                <span class="service-price">${formatPrice(
                  service.price
                )}đ</span>
                <span class="service-duration">${service.duration} phút</span>
            </div>
        </div>
      `;
      elements.servicesContainer.appendChild(serviceItem);

      // Add event listener to service radio
      const serviceRadio = serviceItem.querySelector(".service-radio");
      serviceRadio.addEventListener("change", function () {
        state.selectedServiceId = this.value;
        elements.serviceIdInput.value = state.selectedServiceId;
        elements.continueStep2Button.disabled = false;
      });
    });
  }

  /**
   * Generate date options for the next two weeks
   */
  function generateDateOptions() {
    elements.dateList.innerHTML = "";

    const dates = getNextTwoWeeksDates();
    dates.forEach((date) => {
      const dateItem = document.createElement("div");
      dateItem.className = "date-item";
      dateItem.innerHTML = `
        <input type="radio" name="booking_date" id="date-${date.value}" value="${date.value}" class="date-radio">
        <label for="date-${date.value}" class="date-label-text">${date.label}</label>
      `;
      elements.dateList.appendChild(dateItem);

      // Add event listener to date radio
      const dateRadio = dateItem.querySelector(".date-radio");
      dateRadio.addEventListener("change", function () {
        state.selectedDate = this.value;
        elements.bookingDateInput.value = state.selectedDate;
        elements.timeSelection.classList.remove("hidden");
        generateTimeOptions();
        updateBookButton();
      });
    });
  }

  /**
   * Generate time options for the selected date
   */
  function generateTimeOptions() {
    elements.timeList.innerHTML = "";

    const times = getAvailableTimes();
    times.forEach((time) => {
      const timeItem = document.createElement("div");
      timeItem.className = "time-item";
      timeItem.innerHTML = `
        <input type="radio" name="booking_time" id="time-${time.value}" value="${time.value}" class="time-radio">
        <label for="time-${time.value}" class="time-label-text">${time.label}</label>
      `;
      elements.timeList.appendChild(timeItem);

      // Add event listener to time radio
      const timeRadio = timeItem.querySelector(".time-radio");
      timeRadio.addEventListener("change", function () {
        state.selectedTime = this.value;
        elements.bookingTimeInput.value = state.selectedTime;
        updateBookButton();
      });
    });
  }

  /**
   * Update book button state
   */
  function updateBookButton() {
    if (
      state.userId &&
      state.selectedBranchId &&
      state.selectedServiceId &&
      state.selectedDate &&
      state.selectedTime
    ) {
      elements.bookButton.disabled = false;
    } else {
      elements.bookButton.disabled = true;
    }
  }

  /**
   * Get dates for the next two weeks
   * @returns {Array} List of dates
   */
  function getNextTwoWeeksDates() {
    const dates = [];
    const today = new Date();

    for (let i = 0; i < 14; i++) {
      const date = new Date(today);
      date.setDate(today.getDate() + i);

      const value = formatDate(date);
      const label = formatDateLabel(date);

      dates.push({ value, label });
    }

    return dates;
  }

  /**
   * Get available times for booking
   * @returns {Array} List of times
   */
  function getAvailableTimes() {
    const times = [];
    const start = 9; // 9AM
    const end = 19; // 7PM

    for (let hour = start; hour < end; hour++) {
      times.push({
        value: `${padZero(hour)}:00`,
        label: `${hour}:00`,
      });

      times.push({
        value: `${padZero(hour)}:30`,
        label: `${hour}:30`,
      });
    }

    return times;
  }

  /**
   * Show loading overlay
   */
  function showLoading() {
    elements.loadingOverlay.classList.remove("hidden");
  }

  /**
   * Hide loading overlay
   */
  function hideLoading() {
    elements.loadingOverlay.classList.add("hidden");
  }

  /**
   * Show alert message
   * @param {string} message - Alert message
   */
  function showAlert(message) {
    alert(message);
  }

  /**
   * Validate email format
   * @param {string} email - Email to validate
   * @returns {boolean} Validation result
   */
  function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }

  /**
   * Format date to YYYY-MM-DD
   * @param {Date} date - Date to format
   * @returns {string} Formatted date
   */
  function formatDate(date) {
    const year = date.getFullYear();
    const month = padZero(date.getMonth() + 1);
    const day = padZero(date.getDate());

    return `${year}-${month}-${day}`;
  }

  /**
   * Format date label
   * @param {Date} date - Date to format
   * @returns {string} Formatted date label
   */
  function formatDateLabel(date) {
    const dayNames = [
      "Chủ Nhật",
      "Thứ Hai",
      "Thứ Ba",
      "Thứ Tư",
      "Thứ Năm",
      "Thứ Sáu",
      "Thứ Bảy",
    ];
    const dayName = dayNames[date.getDay()];
    const day = padZero(date.getDate());
    const month = padZero(date.getMonth() + 1);

    return `${dayName}, ${day}/${month}`;
  }

  /**
   * Pad number with leading zero if needed
   * @param {number} num - Number to pad
   * @returns {string} Padded number
   */
  function padZero(num) {
    return num.toString().padStart(2, "0");
  }

  /**
   * Format price with thousand separator
   * @param {number} price - Price to format
   * @returns {string} Formatted price
   */
  function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
});
