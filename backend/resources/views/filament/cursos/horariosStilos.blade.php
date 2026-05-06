<style>


:root {
        --ev-green:          #16a34a;
        --ev-green-light:    #dcfce7;
        --ev-green-border:   #86efac;
        --ev-red:            #dc2626;
        --ev-red-light:      #fee2e2;
        --ev-red-border:     #fca5a5;
        --ev-amber:          #d97706;
        --ev-amber-light:    #fef3c7;
        --ev-amber-border:   #fcd34d;
        --ev-gray:           #6b7280;
        --ev-gray-light:     #f3f4f6;
        --ev-gray-border:    #d1d5db;
        --ev-surface-dark: var(--gray-900);
        --ev-surface-dark-alt: color-mix(in oklab, var(--gray-900) 88%, var(--color-white));
        --ev-border-dark: color-mix(in oklab, var(--color-white) 10%, transparent);
        --ev-blue:           #2563eb;
        --ev-blue-light:     #dbeafe;
        --ev-blue-border:    #93c5fd;
        --ev-indigo:         #4f46e5;
        --ev-radius:         12px;
        --ev-shadow:         0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
    }


/* Estilos para la pantalla de Horarios (migrados desde la vista) */
* { box-sizing: border-box; }



.container { max-width: 1280px; margin: 0 auto; padding: 20px 16px 28px; }

.top { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }

h1 { margin: 0; font-size: 1.35rem; }

.muted { color: #6b7280; font-size: 0.92rem; }

.grillaContainer { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; margin-bottom: 14px; }
.dark .grillaContainer {  background:var(--ev-surface-dark); border-color:var(--ev-border-dark); }



.grillaContainer h2 { margin: 0 0 10px; font-size: 1.05rem; }

.grillaContainer p { margin: 0 0 10px; color: #6b7280; font-size: 0.9rem; }

table { width: 100%; border-collapse: collapse; }

th, td { border: 1px solid #e5e7eb; vertical-align: top; text-align: left; padding: 8px; }

.dark th, .dark td { border-color: var(--ev-border-dark); }

/* Anchos específicos para la tabla de asignaciones */
.col-materia { width: 20%; }
.col-docente { width: 34%; }
.col-sit { width: 10%; }

@media (max-width: 900px) {
    .col-materia, .col-docente, .col-sit { width: auto; }
}

thead th { background: #f3f4f6; }
.dark thead th { background: var(--ev-gray-dark); color: #e5e7eb; }

input, select { width: 100%; min-height: 34px; border: 1px solid #d1d5db; border-radius: 6px; padding: 5px 8px; background: #fff; }

.dark input, .dark select {  background: var(--ev-surface-dark-alt); border-color: var(--ev-border-dark); color: #e5e7eb; }

.bloque { min-width: 170px; }

.actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn { display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border-radius: 6px; border: 1px solid transparent; text-decoration: none; cursor: pointer; font-size: 0.9rem; }

.btn-save { background: #1d4ed8; color: #fff; }


.btn-back { background: #fff; color: #1f2937; border-color: #d1d5db; }

/* Efectos hover, foco y adaptación a modo oscuro para botones */
.btn { transition: background-color .15s ease, color .15s ease, transform .08s ease, box-shadow .12s ease; }
.btn:focus { outline: none; box-shadow: 0 0 0 3px color-mix(in oklab, var(--ev-blue) 12%, transparent); }

.btn:hover { transform: translateY(-2px); }

.btn-save {
    background: var(--ev-blue);
    color: #fff;
    border-color: var(--ev-blue-border);
}
.btn-save:hover { background: color-mix(in oklab, var(--ev-blue) 88%, black 12%); }
.dark .btn-save { background: color-mix(in oklab, var(--ev-blue) 78%, var(--color-white) 8%); color: #fff; }
.dark .btn-save:hover { background: color-mix(in oklab, var(--ev-blue) 66%, black 20%); }

.btn-back {
    background: transparent;
    color: var(--ev-gray);
    border-color: var(--ev-gray-border);
}
.btn-back:hover { background: color-mix(in oklab, var(--ev-gray-light) 90%, transparent 10%); }
.dark .btn-back { background: transparent; color: #e5e7eb; border-color: var(--ev-border-dark); }
.dark .btn-back:hover { background: color-mix(in oklab, var(--ev-border-dark) 90%, transparent 10%); }

.btn-assign { background: var(--ev-indigo); color: #fff; border-color: var(--ev-blue-border); }
.btn-assign:hover { background: color-mix(in oklab, var(--ev-indigo) 80%, black 18%); }

.dark .btn-assign:hover { background: color-mix(in oklab, var(--ev-indigo) 60%, black 18%); }

.btn-ghost {
    background: transparent;
    color: var(--ev-gray);
    border: 1px dashed var(--ev-gray-border);
    width: 100%;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.btn-ghost:hover { background: color-mix(in oklab, var(--ev-gray-light) 50%, transparent 50%); border-style: solid; }
.dark .btn-ghost { color: #e5e7eb; border-color: var(--ev-border-dark); }
.dark .btn-ghost:hover { background: color-mix(in oklab, var(--ev-border-dark) 50%, transparent 50%); }

.btn[disabled], .btn.disabled { opacity: .6; cursor: not-allowed; transform: none; }

.flash { padding: 10px; border-radius: 6px; margin-bottom: 10px; }

.flash-ok { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }

.flash-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

.hint { color: #6b7280; font-size: 0.82rem; }






@media (max-width: 900px) {
    .section { padding: 12px; }
}




.formModal{

    display:none;
    position:fixed; 
    inset:0;
   background-color: rgba(0,0,0,0.4);
    z-index:50;
    align-items:center;
    justify-content:center;
   

}

.modalForm{

    border : 1px solid #d1d5db;
    background:#fff;
    border-radius:8px;
    padding:16px;
    width: 520px;
    max-width: 100%;

   
    box-shadow: var(--ev-shadow);
   
}


.dark .modalForm{
    background: var(--ev-surface-dark-alt);
    border-color: var(--ev-border-dark);
    color: #e5e7eb;
}



.docente-suggestions{
    position:absolute; 
    left:0; 
    right:0; 
    background:#fff; 
    border:1px solid #ddd; 
    max-height:200px; 
    overflow:auto; 
    z-index:40; 
    display:none;
}

.dark .docente-suggestions{
    background: var(--ev-surface-dark-alt);
    border-color: var(--ev-border-dark);
    color: #e5e7eb;
}



.docente-suggestionsModal{

    position:absolute; 
    left:0; 
    right:0; 
    background:#fff; 
    border:1px solid #ddd; 
    width: 500px; 
    min-width: 0;
    
    z-index:80; 
  
}


.docente-suggestionsModal  :hover{
  background-color: #d1d5db;

  
}


.dark .docente-suggestionsModal  :hover{
  background-color: #454545;

 
  
}






.dark .docente-suggestionsModal{
    background: var(--ev-surface-dark-alt);
    border-color: var(--ev-border-dark);
    color: #e5e7eb;

}






.relative{
    position:relative;
   
}

.docente-search-row {


    width: 100%;
    min-height: 34px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 5px 8px;
    background: #fff;
}

.docente-suggestions-row{
    position:absolute; 
    left:0; 
    right:0; 
    background:#fff; 
    border:1px solid #ddd; 
   
    overflow:auto; 
    z-index:30; 
   
}
.dark .docente-suggestions-row{
    background: var(--ev-surface-dark-alt);
    border-color: var(--ev-border-dark);
    color: #e5e7eb;
}




</style>