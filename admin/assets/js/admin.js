document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle functionality
  const sidebarToggle = document.querySelector(".sidebar-toggle")
  const sidebar = document.querySelector(".admin-sidebar")
  const content = document.querySelector(".admin-content")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("expanded")
      content.classList.toggle("sidebar-expanded")
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (event) => {
    const isMobile = window.innerWidth <= 576
    const isClickInsideSidebar = sidebar.contains(event.target)
    const isClickOnToggle = sidebarToggle.contains(event.target)

    if (isMobile && !isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains("expanded")) {
      sidebar.classList.remove("expanded")
      content.classList.remove("sidebar-expanded")
    }
  })

  // Responsive adjustments on window resize
  window.addEventListener("resize", () => {
    if (window.innerWidth > 576 && window.innerWidth <= 768) {
      sidebar.classList.remove("expanded")
      content.classList.remove("sidebar-expanded")
    } else if (window.innerWidth > 768) {
      sidebar.classList.remove("expanded")
      content.classList.remove("sidebar-expanded")
    }
  })

  // Status filter functionality
  const statusFilter = document.getElementById("status-filter")
  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      const form = this.closest("form")
      if (form) {
        form.submit()
      }
    })
  }

  // Date range picker initialization
  const dateRangeInputs = document.querySelectorAll(".date-input")
  if (dateRangeInputs.length) {
    dateRangeInputs.forEach((input) => {
      input.addEventListener("change", function () {
        const form = this.closest("form")
        if (form) {
          form.submit()
        }
      })
    })
  }

  // Confirm delete actions
  const deleteButtons = document.querySelectorAll(".btn-delete")
  if (deleteButtons.length) {
    deleteButtons.forEach((button) => {
      button.addEventListener("click", (e) => {
        if (!confirm("Bạn có chắc chắn muốn xóa?")) {
          e.preventDefault()
        }
      })
    })
  }

  // Confirm appointment status changes
  const statusButtons = document.querySelectorAll(".btn-confirm, .btn-cancel")
  if (statusButtons.length) {
    statusButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        const status = this.classList.contains("btn-confirm") ? "xác nhận" : "hủy"
        if (!confirm(`Bạn có chắc chắn muốn ${status} lịch hẹn này?`)) {
          e.preventDefault()
        }
      })
    })
  }
})
