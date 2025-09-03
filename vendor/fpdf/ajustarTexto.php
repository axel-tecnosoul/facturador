<?php
require('fpdf.php');
class PDF extends FPDF {
	var $tablewidths;
	var $footerset;

	function _beginpage($orientation, $size) {
		$this->page++;
		// Resuelve el problema de sobrescribir una página si ya existe.
		if(!isset($this->pages[$this->page])) 
			$this->pages[$this->page] = '';
		$this->state  =2;
		$this->x = $this->lMargin;
		$this->y = $this->tMargin;
		$this->FontFamily = '';

		// Compruebe el tamaño y la orientación.
		if($orientation=='')
			$orientation = $this->DefOrientation;
		else
			$orientation = strtoupper($orientation[0]);
		if($size=='')
			$size = $this->DefPageSize;
		else
			$size = $this->_getpagesize($size);
		if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1]){
			// Nuevo tamaño o la orientación
			if($orientation=='P'){
				$this->w = $size[0];
				$this->h = $size[1];
			}else{
				$this->w = $size[1];
				$this->h = $size[0];
			}
			$this->wPt = $this->w*$this->k;
			$this->hPt = $this->h*$this->k;
			$this->PageBreakTrigger = $this->h-$this->bMargin;
			$this->CurOrientation = $orientation;
			$this->CurPageSize = $size;
		}
		if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
			$this->PageSizes[$this->page] = array($this->wPt, $this->hPt);
	}

	function Footer() {
		// Compruebe si pie de página de esta página ya existe ( lo mismo para Header ( ) )
		if(!isset($this->footerset[$this->page])) {
			$this->SetY(-15);
			// Numero de Pagina
			$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
			// Conjunto Footerset
			$this->footerset[$this->page] = true;
		}
	}

	function morepagestable($datas, $lineheight=13) {
		// Algunas cosas para establecer y ' recuerdan '
		$l = $this->lMargin;
		$startheight = $h = $this->GetY();
		$startpage = $currpage = $maxpage = $this->page;
		
		// Calcular todo el ancho
		$fullwidth = 0;
		foreach($this->tablewidths AS $width) {
			$fullwidth += $width;
		}

		 // Ahora vamos a empezar a escribir la tabla
		foreach($datas AS $row => $data) {
			$this->page = $currpage;
			// Escribir los bordes horizontales
			$this->Line($l,$h,$fullwidth+$l,$h);
		
			// Escribir el contenido y recordar la altura de la más alta columna
			foreach($data AS $col => $txt) {
				$this->page = $currpage;
				$this->SetXY($l,$h);
				$this->MultiCell($this->tablewidths[$col],$lineheight,$txt);
				$l += $this->tablewidths[$col];
		
				if(!isset($tmpheight[$row.'-'.$this->page]))
					$tmpheight[$row.'-'.$this->page] = 0;
				if($tmpheight[$row.'-'.$this->page] < $this->GetY()) {
					$tmpheight[$row.'-'.$this->page] = $this->GetY();
				}
				if($this->page > $maxpage)
					$maxpage = $this->page;
			}
		
			// Obtener la altura estábamos en la última página utilizada
			$h = $tmpheight[$row.'-'.$maxpage];
		
			//Establecer el "puntero " al margen izquierdo
			$l = $this->lMargin;
		
			// Establecer el "$currpage en la ultima paginia
			$currpage = $maxpage;
		}

		// Dibujar las fronteras
		// Empezamos a añadir una línea horizontal en la última página
		$this->page = $maxpage;
		$this->Line($l,$h,$fullwidth+$l,$h);
		// Ahora empezamos en la parte superior del documento
		for($i = $startpage; $i <= $maxpage; $i++) {
			$this->page = $i;
			$l = $this->lMargin;
			$t  = ($i == $startpage) ? $startheight : $this->tMargin;
			$lh = ($i == $maxpage)   ? $h : $this->h-$this->bMargin;
			$this->Line($l,$t,$l,$lh);
			foreach($this->tablewidths AS $width) {
				$l += $width;
				$this->Line($l,$t,$l,$lh);
			}
		}
		 // Establecerlo en la última página , si no que va a causar algunos problemas
		 $this->page = $maxpage;
	}
}
?>