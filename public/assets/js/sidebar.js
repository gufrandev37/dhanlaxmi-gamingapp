// document.addEventListener("DOMContentLoaded", function () {

//     fetch("/assets/components/sidebar.html")
//         .then(res => res.text())
//         .then(data => {
//             document.getElementById("sidebar-container").innerHTML = data;

         
//             document.querySelectorAll(".js-submenu .subNavHeader")
//                 .forEach(header => {
//                     header.addEventListener("click", () => {
//                         header.parentElement.classList.toggle("active");
//                     });
//                 });
//         })
//         .catch(err => console.error("Sidebar load failed:", err));

// });
fetch("/assets/components/sidebar.html")
  .then(res => res.text())
  .then(html => {
    document.getElementById("sidebar-container").innerHTML = html;

    // ✅ submenu click works on ALL pages
    document.querySelectorAll('.js-submenu .subNavHeader')
      .forEach(header => {
        header.addEventListener('click', () => {
          header.parentElement.classList.toggle('active');
        });
      });
  })
  .catch(err => console.error("Sidebar load error:", err));

