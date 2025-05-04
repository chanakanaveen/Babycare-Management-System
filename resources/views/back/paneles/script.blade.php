<script src="/back/vendors/scripts/core.js"></script>
		<script src="/back/vendors/scripts/script.min.js"></script>
		<script src="/back/vendors/scripts/process.js"></script>
		<script src="/back/vendors/scripts/layout-settings.js"></script>
        <script>
			if( navigator.userAgent.indexOf("Firefox") != -1 ){
				history.pushState(null, null, document.URL);
				window.addEventListener('popstate', function(){
					history.pushState(null, null, document.URL);
				});
			}
		</script>
        <script src="/extra-assets/ijabo/ijabo.min.js"></script>
		<script src="/extra-assets/ijabo/jquery.ijaboViewer.min.js"></script>
		<script src="/extra-assets/ijaboCropTool/ijaboCropTool.min.js"></script>
        <script>
			window.addEventListener('showToastr', function(event){
                  toastr.remove();
				  if( event.detail[0].type === 'info' ){ toastr.info(event.detail[0].message); }
				  else if( event.detail[0].type === 'success' ){ toastr.success(event.detail[0].message); }
				  else if( event.detail[0].type === 'error' ){ toastr.error(event.detail[0].message); }
				  else if( event.detail[0].type === 'warning' ){ toastr.warning(event.detail[0].message); }
				  else{ return false; }
			});
		</script>
