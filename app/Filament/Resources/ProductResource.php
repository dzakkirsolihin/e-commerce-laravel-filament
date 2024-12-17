<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    // protected static ?string $label = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Kategori')
                    ->placeholder('Pilih kategori produk')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Produk')
                    ->placeholder('Masukkan nama produk')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->readOnly()
                    ->maxLength(255)
                    ->unique(Product::class, 'slug', ignoreRecord: true)
                    ->placeholder('Slug akan diisi otomatis setelah mengisi nama produk'),
                TextInput::make('price')
                    ->required()
                    ->integer()
                    ->label('Harga')
                    ->placeholder('Masukkan harga produk'),
                TextInput::make('size')
                    ->required()
                    ->maxLength(255)
                    ->label('Ukuran')
                    ->placeholder('Masukkan ukuran produk'),
                TextInput::make('weight')
                    ->required()
                    ->integer()
                    ->label('Berat Produk Per KG')
                    ->placeholder('Masukkan berat produk per kg'),
                TextInput::make('stock')
                    ->required()
                    ->integer()
                    ->label('Stok Produk')
                    ->placeholder('Masukkan stok produk')
                    ->columnSpan(2),
                
                Repeater::make('members')
                ->relationship('photos')
                ->required()
                ->label('Gambar Produk')
                ->schema([
                    FileUpload::make('path')
                        ->label('Gambar Produk')
                        ->required()
                        ->image()
                        ->directory('product-photos')
                        ->disk('public')
                        ->maxSize(1024)
                        ->imageEditor()
                        ->imageCropAspectRatio('1:1')
                        ->placeholder('Masukkan gambar produk')
                ])
                ->columnSpanFull()
                ->grid([
                    'sm' => 2,
                    'md' => 3,
                    'lg' => 4,
                    'xl' => 5,
                    '2xl' => 6,
                ]),
                Textarea::make('description')
                    ->required()
                    ->label('Deskripsi Produk')
                    ->rows(10)
                    ->autosize()
                    // ->columnSpan()
                    ->placeholder('Masukkan deskripsi produk'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                ImageColumn::make('photos.path')
                    ->label('Gambar Produk')
                    ->stacked()
                    ->limit(3)
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->searchable()
                    ->sortable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Harga Produk')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Ukuran'),
                TextColumn::make('weight')
                    ->label('Berat Produk Per KG')
                    ->suffix('KG'),
                TextColumn::make('stock')
                    ->label('Stok Produk')
                    ->suffix('Pcs'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Kategori')
                    ->placeholder('Pilih kategori produk')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Cari nama produk...');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
