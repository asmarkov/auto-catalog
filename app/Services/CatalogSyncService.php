<?php

namespace App\Services;

use App\Data\XmlOfferData;
use App\Models\BodyType;
use App\Models\CarOffer;
use App\Models\Color;
use App\Models\EngineType;
use App\Models\GearType;
use App\Models\Generation;
use App\Models\Mark;
use App\Models\Model;
use App\Models\Transmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Saloon\XmlWrangler\XmlReader;

class CatalogSyncService
{
    public function sync(string $xml_file_path): void
    {
        $reader = XmlReader::fromFile($xml_file_path);
        $offers_collection = $reader->value('offer')->collectLazy();
        CarOffer::query()->update(['actualize' => false]);
        foreach ($offers_collection as $offer) {
            try {
                DB::transaction(function () use ($offer) {
                    $offer = Arr::mapWithKeys($offer, function (string $item, string $key) {
                        if (is_numeric($item)) {
                            $item = Str::of($item)->toInteger();
                        } else {
                            $item = Str::of($item)->trim()->value();
                            $item = blank($item) ? null : $item;
                        }
                        $key = Str::snake(Str::camel($key));
                        return [$key => $item];
                    });
                    /** @var XmlOfferData $offer_dto */
                    $offer_dto = XmlOfferData::factory()->withoutPropertyNameMapping()->from($offer);
                    $mark = $this->getMark($offer_dto->mark);
                    $model = $this->getModel($offer_dto->model, $mark);
                    $generation = $this->getGeneration($offer_dto->generation, $offer_dto->generation_id, $model);
                    $color = $this->getColor($offer_dto->color);
                    $body_type = $this->getBodyType($offer_dto->body_type);
                    $engine_type = $this->getEngineType($offer_dto->engine_type);
                    $transmission = $this->getTransmission($offer_dto->transmission);
                    $gear_type = $this->getGearType($offer_dto->gear_type);
                    $car_offer_model = CarOffer::findOrNew($offer_dto->id);
                    $car_offer_model->id = $offer_dto->id;
                    $car_offer_model->mark_id = $mark->id;
                    $car_offer_model->model_id = $model->id;
                    $car_offer_model->color_id = $color?->id;
                    $car_offer_model->body_type_id = $body_type?->id;
                    $car_offer_model->engine_type_id = $engine_type?->id;
                    $car_offer_model->transmission_id = $transmission?->id;
                    $car_offer_model->gear_type_id = $gear_type?->id;
                    $car_offer_model->generation_id = $generation?->id;
                    $car_offer_model->manufacture_year = $offer_dto->year;
                    $car_offer_model->mileage = $offer_dto->run;
                    $car_offer_model->actualize = true;
                    $car_offer_model->save();
                });
            } catch (\Throwable $exception) {
                Log::error('Ошибка при создании записи авто', ['exception' => $exception]);
            }
        }

        CarOffer::where('actualize', false)->delete();
        $mark_delete_collect = Mark::leftJoin('car_offers', 'marks.id', '=', 'car_offers.mark_id')->groupBy(
            'marks.id'
        )->havingRaw(
            'count(car_offers.mark_id) = 0'
        )->select('marks.id');
        $model_delete_collect = Model::whereIn('mark_id', $mark_delete_collect->pluck('id'));
        Generation::whereIn('model_id', $model_delete_collect->pluck('id'))->delete();
        $model_delete_collect->delete();
        Mark::whereIn('id', $mark_delete_collect->pluck('id'))->delete();
        $color_delete_collect = Color::leftJoin('car_offers', 'colors.id', '=', 'car_offers.color_id')->groupBy('colors.id')->havingRaw(
            'count(car_offers.mark_id) = 0'
        )->select('colors.id')->pluck('id');
        Color::whereIn('id', $color_delete_collect)->delete();
        $body_types_delete_collection = BodyType::leftJoin('car_offers', 'body_types.id', '=', 'car_offers.body_type_id')->groupBy(
            'body_types.id'
        )->havingRaw(
            'count(car_offers.body_type_id) = 0'
        )->select('body_types.id')->pluck('id');
        BodyType::whereIn('id', $body_types_delete_collection)->delete();
        $engine_types_delete_collection = EngineType::leftJoin('car_offers', 'engine_types.id', '=', 'car_offers.engine_type_id')->groupBy(
            'engine_types.id'
        )->havingRaw(
            'count(car_offers.engine_type_id) = 0'
        )->select('engine_types.id')->pluck('id');
        EngineType::whereIn('id', $engine_types_delete_collection)->delete();
        $transmissions_delete_collection = Transmission::leftJoin('car_offers', 'transmissions.id', '=', 'car_offers.transmission_id')->groupBy(
            'transmissions.id'
        )->havingRaw(
            'count(car_offers.transmission_id) = 0'
        )->select('transmissions.id')->pluck('id');
        Transmission::whereIn('id', $transmissions_delete_collection)->delete();
        $gear_types_delete_collection = GearType::leftJoin('car_offers', 'gear_types.id', '=', 'car_offers.gear_type_id')->groupBy(
            'gear_types.id'
        )->havingRaw(
            'count(car_offers.gear_type_id) = 0'
        )->select('gear_types.id')->pluck('id');
        GearType::whereIn('id', $gear_types_delete_collection)->delete();
    }

    private function getMark(string $mark_name): Mark
    {
        return Mark::firstOrCreate(['name' => $mark_name]);
    }

    private function getModel(string $model_name, Mark $mark_model): Model
    {
        return Model::firstOrCreate(['name' => $model_name, 'mark_id' => $mark_model->id]);
    }

    private function getGeneration(?string $generation_name, ?int $generation_id, Model $model): ?Generation
    {
        return is_null($generation_name) ? null : Generation::firstOrCreate(
            ['id' => $generation_id, 'name' => $generation_name, 'model_id' => $model->id]
        );
    }

    private function getColor(?string $color_name): ?Color
    {
        return is_null($color_name) ? null : Color::firstOrCreate(['name' => $color_name]);
    }

    private function getBodyType(?string $body_type_name): ?BodyType
    {
        return is_null($body_type_name) ? null : BodyType::firstOrCreate(['name' => $body_type_name]);
    }

    private function getEngineType(?string $engine_type_name): ?EngineType
    {
        return is_null($engine_type_name) ? null : EngineType::firstOrCreate(['name' => $engine_type_name]);
    }

    private function getTransmission(?string $transmission_name): ?Transmission
    {
        return is_null($transmission_name) ? null : Transmission::firstOrCreate(['name' => $transmission_name]);
    }

    private function getGearType(?string $gear_type_name): ?GearType
    {
        return is_null($gear_type_name) ? null : GearType::firstOrCreate(['name' => $gear_type_name]);
    }
}
