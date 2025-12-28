<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaspCatalogo extends Model
{
  protected $table = 'fasp_catalogo';

  protected $fillable = [
    'year','entidad','nivel','parent_id',
    'eje','programa','subprograma','capitulo','concepto','partida_generica','bien',
    'nombre',
    'fed_federal','fed_municipal','est_estatal','est_municipal',
    'calc_fed_federal','calc_fed_municipal','calc_est_estatal','calc_est_municipal',
    'unidad_medida','cantidad','persona_cantidad2','rlcf',
  ];

  public function parent(){ return $this->belongsTo(self::class,'parent_id'); }
  public function children(){ return $this->hasMany(self::class,'parent_id'); }

  // Subtotales y total (capturados)
  public function getFedSubtotalAttribute(){ return (float)$this->fed_federal + (float)$this->fed_municipal; }
  public function getEstSubtotalAttribute(){ return (float)$this->est_estatal + (float)$this->est_municipal; }
  public function getFinTotalAttribute(){ return $this->fed_subtotal + $this->est_subtotal; }

  // Subtotales y total (calculados)
  public function getCalcFedSubtotalAttribute(){ return (float)$this->calc_fed_federal + (float)$this->calc_fed_municipal; }
  public function getCalcEstSubtotalAttribute(){ return (float)$this->calc_est_estatal + (float)$this->calc_est_municipal; }
  public function getCalcFinTotalAttribute(){ return $this->calc_fed_subtotal + $this->calc_est_subtotal; }

  public function getTieneDiferenciaAttribute(): bool
  {
    $eps = 0.01;
    return
      abs($this->fed_federal - $this->calc_fed_federal) > $eps ||
      abs($this->fed_municipal - $this->calc_fed_municipal) > $eps ||
      abs($this->est_estatal - $this->calc_est_estatal) > $eps ||
      abs($this->est_municipal - $this->calc_est_municipal) > $eps;
  }

}
