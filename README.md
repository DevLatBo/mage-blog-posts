# Elaboración de Grid Custom

Este módulo en su versión 1.0.1 contiene una grid elaborada para el 
admin a partir de una tabla custom y describiremos acá el paso a paso 
para construir nuestra propia grid usando este módulo como orientación y/o 
referencia.

### Propósito
Este módulo tiene el objetivo de usarlo como referencia para poder 
armar una grid en Magento 2 en el admin para poder ahorrar tiempo 
en el desarrollo y evitar errores con la estructura del layout XML 
y problemas que pueden costar más tiempo y atrasar con 
la elaboración de tareas.

## PASOS
* [Creacion tabla para BD](#creacion-tabla-para-bd)
* [Insertar Datos a Tabla](#insertar-datos-a-tabla)
* [Creacion de Model](#creacion-de-model)
* [ACL Resources Roles](#acl-resources-roles)
* [UI Component](#ui-component)
* [Menu](#menu)
* [Virtual Type](#virtual-type)
* [UI Listing Layout](#ui-listing-layout)
* [DataProvider](#dataprovider)
* [Form](#form)

## Creacion tabla para BD
Como primer paso se recomienda crear una tabla para la base de 
datos, para este ejemplo se crea la tabla `devlat_blog_post`.

Cabe declarar de que todo nombre para una tabla de la base de datos debe 
ser en **singular**, esta tabla que se crea contiene los datos como ser:

* ID (Primary Key).
* Title
* Publish Date
* Content


Podrá ver en el file `etc/db_schema.xml` para mas detalles.

## Insertar Datos a Tabla
Para esta acción requeriremos de ayuda de un Patch, usted puede 
colocar los datos que desee, pero recomiendo  que pueda tener como 10 filas 
insertadas en la tabla `blog_post`.

En éste módulo encontrará el patch en `Devlat\Blog\Setup\Patch\Data\InitialPosts` en 
donde podrá ver que se insertan datos gracias a una dependencia (`resourceConnection`).

## Creacion de Model
Primero se procede con crear la clase Modelo Post `Devlat\Blog\Model\Post`, pero antes de crear una clase modelo 
para una tabla se recomienda crear el object interface `Devlat\Blog\Api\Data\PostInterface` definiendo los 
getters y setters. El object interface se implementa en la clase Model Post.

RECUERDA que hay que usar el preference con el modelo y el object interface en `etc/di.xml`:
```xml
<preference for="Devlat\Blog\Api\Data\PostInterface" type="Devlat\Blog\Model\Post" />
```

Luego del Model se crea el ResourceModel `Devlat\Blog\Model\ResourceModel\Post` donde declaramos la tabla 
en `_construct` con su `id`. Esto nos sera util para el CRUD (Create, Read, Update and Delete).
En pocas palabras el ResourceModel facilita la comunicación con la base de datos.

Finalmente tenemos Collections `Devlat\Blog\Model\ResourceModel\Post\Collection` el cual representa 
el conjunto de datos de la tabla `blog_post`, justamente para ello declaramos en `_construct` el Model y ResourceModel.

## ACL Resources Roles
Se crea un archivo en `etc/acl.xml` donde declaramos los Roles necesarios para el admin 
dentro de `Magento_Backend::admin`:
```xml
    <resources>
        <resource id="Magento_Backend::admin">
            <resource id="Devlat_Blog::blog" title="Blog" translate="title" sortOrder="51">
                <resource id="Devlat_Blog::posts" title="Posts" translate="title">
                    <resource id="Devlat_Blog::post_save" title="Save" translate="title"/>
                    <resource id="Devlat_Blog::post_delete" title="Delete" translate="title"/>
                </resource>
            </resource>
        </resource>
    </resources>
```
Donde
* `Devlat_Blog::blog` Usamos para el item menu que crearemos.
* `Devlat_Blog::posts` para el index.
* `Devlat_Blog::post_save` Agrega o editar post
* `Devlat_Blog::post_delete` Eliminar post.

## UI Component
Se crea el file: `view/adminhtml/layout/blog_post_index.xml`
Acá es donde se declará únicamente el componente a usar para el grid:
```xml
    <body>
        <referenceContainer name="content">
            <uiComponent name="devlat_blog_post_listing"/>
        </referenceContainer>
    </body>
```
NOTA: El nombre lo declaro de la siguiente la convencion:
* **devlat** es el vendor.
* **blog** es el módulo.
* **post** es el nombre de la clase Model (Entity) que creamos para la tabla custom.

## Menu
Creamos el menu para el admin en base a los ACL Resources Roles creados.
Necesitamos para ello el archivo `etc/adminhtml/menu.xml` y de esa forma 
creamos el menu y su item:
```xml
    <menu>
        <add
            id="Devlat_Blog::blog"
            title="Blog"
            translate="title"
            module="Devlat_Blog"
            sortOrder="51"
            resource="Devlat_Blog::blog"
        />
        <add
            id="Devlat_Blog::posts"
            title="Posts"
            translate="title"
            module="Devlat_Blog"
            sortOrder="10"
            action="blog/post"
            resource="Devlat_Blog::posts"
            parent="Devlat_Blog::blog"/>
    </menu>
```
Por lo que BLOG es el boton visible en el menu y POSTS es el item que nos 
redirigirá al grid y para ello requerimos de un controlador: `Devlat\Blog\Controller\Adminhtml\Post\Index`.

## Virtual Type
Se tiene creado el archivo etc/di.xml donde definimos el source data con la 
tabla `blog_post` que usaremos en el grid:
```xml
    <virtualType name="Devlat\Blog\Model\ResourceModel\Post\Grid\Collection"
                 type="Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult">
        <arguments>
            <argument name="mainTable" xsi:type="string">blog_post</argument>
            <argument name="resourceModel" xsi:type="string">Devlat\Blog\Model\ResourceModel\Post</argument>
        </arguments>
    </virtualType>
```
Y en type para devlat_blog_post_listing_data_source asignamos la clase Collection:
```xml
    <type name="Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory">
        <arguments>
            <argument name="collections" xsi:type="array">
                <item name="devlat_blog_post_listing_data_source" xsi:type="string">Devlat\Blog\Model\ResourceModel\Post\Grid\Collection</item>
            </argument>
        </arguments>
    </type>
```

Esto es importante para la grid que iremos construyendo.

## UI Listing Layout

Creamos antes el controlador que redirigirá al grid en el admin, tal como declaramos 
en `menu.xml` para POSTS en action `blog/post`:
`Devlat\Blog\Controller\Adminhtml\Post\Index`.
En donde se setea el titulo y se deja activo el boton de BLOG:
```php
$resultPage->setActiveMenu('Devlat_Blog::posts');
```

Despues de eso se crea el archivo `devlat_blog_post_listing.xml` con ubicación en 
`view/adminhtml/ui_component/devlat_blog_post_listing.xml`. Recuerda que el nombre 
del archivo es donde declaramos en `view/adminhtml/layout/blog_post_index.xml`.
```xml 
<uiComponent name="devlat_blog_post_listing"/>
```

Iniciando con la construccion del grid
```xml
<?xml version="1.0" encoding="UTF-8"?>

<listing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">
...
    
</listing>
```
Preparamos el grid para que puedan ser mostrados los datos (source data):

```xml
    <argument name="data" xsi:type="array">
        <item name="js_config" xsi:type="array">
            <item name="provider" xsi:type="string">
                devlat_blog_post_listing.devlat_blog_post_listing_data_source
            </item>
        </item>
        <item name="buttons" xsi:type="array">
            <item name="add" xsi:type="array">
                <item name="name" xsi:type="string">add</item>
                <item name="label" xsi:type="string" translate="true">Add New Post</item>
                <item name="class" xsi:type="string">primary</item>
                <item name="url" xsi:type="string">*/*/add</item>
            </item>
        </item>
    </argument>
    <settings>
        <deps>
            <dep>devlat_blog_post_listing.devlat_blog_post_listing_data_source</dep>
        </deps>
        <spinner>devlat_blog_post_columns</spinner>
    </settings>
    <dataSource name="devlat_blog_post_listing_data_source" component="Magento_Ui/js/grid/provider">
        <settings>
            <updateUrl path="mui/index/render"/>
            <storageConfig>
                <param name="indexField" xsi:type="string">id</param>
            </storageConfig>
        </settings>
        <aclResource>Devlat_Blog::posts</aclResource>
        <dataProvider name="devlat_blog_post_listing_data_source"
                      class="Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider">
            <settings>
                <requestFieldName>id</requestFieldName>
                <primaryFieldName>id</primaryFieldName>
            </settings>
        </dataProvider>
    </dataSource>
```
Favor tomar nota lo siguiente del XML, preparamos todo los datos para columnas (**columns**), tambien 
preparamos el boton de Add New Post, en `buttons` dando el nombre, la ruta y el tipo de boton.

``` xml
<item name="buttons" xsi:type="array">
    <item name="add" xsi:type="array">
        <item name="name" xsi:type="string">add</item>
        <item name="label" xsi:type="string" translate="true">Add New Post</item>
        <item name="class" xsi:type="string">primary</item>
        <item name="url" xsi:type="string">*/*/add</item>
    </item>
</item>
```

Es importante declarar spinner para indicar que los datos se estan cargando.
```xml
    <spinner>devlat_blog_post_columns</spinner>
```
En Data Provider se encarga de obtener y preparar los datos:
```xml
<dataProvider name="devlat_blog_post_listing_data_source"
                      class="Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider">
    <settings>
        <requestFieldName>id</requestFieldName>
        <primaryFieldName>id</primaryFieldName>
    </settings>
</dataProvider>
```
**Nodo requestFieldName** es el nombre del campo clave primaria en la base de datos.

**Nodo primaryFieldName** es el nombre del parámetro que se envía en las URLs o peticiones 
(por ejemplo, cuando haces clic en editar un ítem).

Dntro de dataSource notese que usamos el storageConfig:
```xml
<dataSource name="devlat_blog_post_listing_data_source" component="Magento_Ui/js/grid/provider">
    <settings>
        <updateUrl path="mui/index/render"/>
        <storageConfig>
            <param name="indexField" xsi:type="string">id</param>
        </storageConfig>
    </settings>
</dataSource>
```

Se encarga de que los datos puedan visualizarse correctamente despues de ejecutar una acción, ya 
sea despues de editar, agregar o buscar algun dato. Sin esto, los datos o filas se van a ir repitiendo o 
no se visualizan en la grid correctamente.

Para controlar el acceso a la grid incluimos dentro de dataSource incluimos el AclResource:
```xml
<dataSource name="devlat_blog_post_listing_data_source" component="Magento_Ui/js/grid/provider">
    ...
    <aclResource>Devlat_Blog::posts</aclResource>
    ...
</dataSource>
```

Las columnas se las elabora de la siguiente manera:
```xml
    <columns name="devlat_blog_post_columns">
        <settings>
            <childDefaults>
                <param name="fieldAction" xsi:type="array">
                    <item name="provider" xsi:type="string">devlat_blog_post_listing.devlat_blog_post_listing.devlat_blog_post_columns_editor</item>
                    <item name="target" xsi:type="string">startEdit</item>
                    <item name="params" xsi:type="array">
                        <item name="0" xsi:type="string">${ $.$data.rowIndex }</item>
                        <item name="1" xsi:type="boolean">true</item>
                    </item>
                </param>
            </childDefaults>
            <editorConfig>
                <param name="clientConfig" xsi:type="array">
                    <item name="saveUrl" xsi:type="url" path="*/*/inlineEdit"/>
                    <item name="validateBeforeSave" xsi:type="boolean">false</item>
                </param>
                <param name="selectProvider" xsi:type="string">
                    devlat_blog_post_listing.devlat_blog_post_listing.devlat_blog_post_columns.ids
                </param>
                <param name="indexField" xsi:type="string">id</param>
                <param name="enabled" xsi:type="boolean">true</param>
            </editorConfig>
        </settings>
        <selectionsColumn name="ids">
            <settings>
                <indexField>id</indexField>
                <resizeEnabled>false</resizeEnabled>
                <resizeDefaultWidth>55</resizeDefaultWidth>
            </settings>
        </selectionsColumn>
        <column name="id">
            <settings>
                <label translate="true">ID</label>
                <filter>text</filter>
                <sorting>asc</sorting>
                <visible>false</visible>
            </settings>
        </column>
        <column name="title">
            <settings>
                <label translate="true">Title</label>
                <filter>text</filter>
                <editor>
                    <editorType>text</editorType>
                    <validation>
                        <rule name="required-entry" xsi:type="boolean">true</rule>
                    </validation>
                </editor>
            </settings>
        </column>
        <column name="publish_date" component="Magento_Ui/js/grid/columns/date">
            <settings>
                <label translate="true">Publish Date</label>
                <filter>dateRange</filter>
                <dataType>date</dataType>
                <dateFormat>dd/MM/Y</dateFormat>
                <editor>
                    <editorType>date</editorType>
                    <validation>
                        <rule name="required-entry" xsi:type="boolean">true</rule>
                    </validation>
                </editor>
            </settings>
        </column>
        <actionsColumn name="actions" class="Devlat\Blog\Ui\Component\Listing\Grid\Column\Action">

        </actionsColumn>
    </columns>
```
Note que tenemos selectionsColumn que es usado para poder generar los checkbox para cada fila.

Actualmente se esta usando para la edicion inlineEdit, es decir si haces clic en una linea puedes 
editar los datos desde la grid, es por eso que declaramos el startEdit y agregando en las columnas
que deseamos que puedan ser editables el nodo item con name "editor"

NOTA: Vease de que el inlineEdit requiere de un controlador, por lo que tenemos el controlador desarrollado 
`Devlat\Blog\Controller\Adminhtml\Post\InlineEdit` para ejecutar esta tarea.

```xml
<editorConfig>
    <param name="clientConfig" xsi:type="array">
        <item name="saveUrl" xsi:type="url" path="*/*/inlineEdit"/>
        <item name="validateBeforeSave" xsi:type="boolean">false</item>
    </param>
    <param name="selectProvider" xsi:type="string">
        devlat_blog_post_listing.devlat_blog_post_listing.devlat_blog_post_columns.ids
    </param>
    <param name="indexField" xsi:type="string">id</param>
    <param name="enabled" xsi:type="boolean">true</param>
</editorConfig>
```

Puedes tambien usar el applyAction para poder hacer cada fila clickeable y nos redirija al edit.
Recuerda entonces de que si deseas que cada fila sea cliqueable para redigir al edit form toma en cuenta esto:
```xml
    <childDefaults>
        <param name="fieldAction" xsi:type="array">
            <item name="provider" xsi:type="string">
                devlat_blog_post_listing.devlat_blog_post_listing.devlat_blog_post_columns.actions
            </item>
            <item name="target" xsi:type="string">applyAction</item>
            <item name="params" xsi:type="array">
                <item name="0" xsi:type="string">edit</item>
                <item name="1" xsi:type="string">${ $.$data.rowIndex }</item>
            </item>
        </param>
    </childDefaults>
```

Luego como verán tenemos las otras columnas para title y publish_date con sus respectivas configuraciones, para 
actionsColumn usamos para poder generar enlaces tipo dropdown de EDIT y DELETE. **OJO**, este requiere 
de una clase Action como se ve en el código, el path es `Devlat\Blog\Ui\Component\Listing\Grid\Column\Action`, gracias 
a esta clase en método `prepareDataSource` podremos generar el enlace o enlaces (dropdown).
Recuerda que tenemos que crear el controlador para DELETE, por ello  encontraras el mismo en:
`Devlat\Blog\Controller\Adminhtml\Post\Delete` y mencionar de igual forma para el ADD/EDIT el 
controlador para redireccionar `Devlat\Blog\Controller\Adminhtml\Post\Add`.

Procedemos ahora a armar el Toolbar (`<listingToolbar name="listing_top">`), el cual necesitamos:
* Para paginación agrega: `<paging name="listing_paging"/>`
* Para bookmark: `<bookmark name="bookmarks"/>`
* Para agregar búsqueda full text search: `<filterSearch name="fulltext"/>`
* Puedes agregar filtros por default, para este modulo agregamos uno para publish_date:
```xml
    <filters name="listing_filters">
        <argument name="data" xsi:type="array">
            <item name="config" xsi:type="array">
                <item name="applied" xsi:type="array">
                    <item name="publish_date" xsi:type="array">
                        <item name="from" xsi:type="string">10/10/2010</item>
                        <item name="to" xsi:type="string">01/01/2024</item>
                    </item>
                </item>
            </item>
        </argument>
    </filters>
```

### IMPORTANTE:
1. Para el funcionamiento del filtersearch y que busque por el title, hay que volver al archivo 
db_schema.xml y agregar el siguiente nodo para proceder con la prueba del filterSearch en base al `title`:
```xml
    <index referenceId="BLOG_POST_TITLE" indexType="fulltext">
            <column name="title"/>
    </index>
```
2. Importante tomar en cuenta de que si agregas una nueva columna a la grid desde el XML, lo agregará
   al final y no en una posición deseada, para que tome efecto real, dirijase a la BD a la tabla
   ui_bookmarks y busque por el namespace `devlat_settings_tracker_listing` y borrelo, una vez hecho eso
   limpie cache y vera que su columna estara en una posicion deseada.

* Para MassActton: Es un Dropdown donde agregamos el delete para ejecutar el eliminado de uno o mas items en grid:
```xml
<massaction name="listing_massaction" component="Magento_Ui/js/grid/tree-massactions">
     <settings>
         <selectProvider>devlat_blog_post_listing.devlat_blog_post_listing.devlat_blog_post_columns.ids</selectProvider>
         <indexField>id</indexField>
     </settings>
     <action name="delete">
         <settings>
             <type>delete</type>
             <label translate="true">Delete</label>
             <url path="*/*/massDelete"/>
             <confirm>
                 <title translate="true">Delete items</title>
                 <message translate="true">Are you sure you want to delete the selected items?</message>
             </confirm>
         </settings>
     </action>
</massaction>
```

Requerimos de un controlador MassDelete: `Devlat\Blog\Controller\Adminhtml\Post\MassDelete` donde aplicamos la 
lógica  de como se ejecutará la eliminación de los items en tabla `blog_post`.

## DataProvider
Creamos como siguiente paso el data provider en la siguiente ruta: `Devlat\Blog\Ui\DataProvider\Post`.
justo ahi necesitaremos para tener datos mejor estructurados para nuestros UI Components.

## Form
Creamos el UI component para el add en `view/adminhtml/layout/blog_post_add.xml` en donde creamos el componente:

```xml
    <body>
        <referenceContainer name="content">
            <uiComponent name="devlat_blog_post_form"/>
        </referenceContainer>
    </body>
```

El cual necesitamos para el form, ahora creando el archivo `view/adminhtml/ui_component/devlat_blog_post_form.xml`.
Acá configuramos el form component para que guarde la data:
```xml
    <argument name="data" xsi:type="array">
        <item name="template" xsi:type="string">templates/form/collapsible</item>
    </argument>
    <settings>
        <namespace>devlat_blog_post_form</namespace>
        <dataScope>data</dataScope>
    </settings>
```
Por ello es que precisamos de usar el dataScope, la data sera guardada en la propiedad "data", y la razon es que en JS
 en módulo Magento_Ui en `view/base/web/js/form/provider.js` se tiene definido así la propiedad en metodo **save()**.

Lo siguiente es agregar el dataSource con el dataProvider incluido:
```xml
    <dataSource name="devlat_blog_post_data_source">
        <argument name="data" xsi:type="array">
            <item name="js_config" xsi:type="array">
                <item name="component" xsi:type="string">Magento_Ui/js/form/provider</item>
            </item>
        </argument>
        <dataProvider class="Devlat\Blog\Ui\DataProvider\Post" name="devlat_blog_post_data_source">
            <settings>
                <requestFieldName>id</requestFieldName>
                <primaryFieldName>id</primaryFieldName>
            </settings>
        </dataProvider>
    </dataSource>
```

El name de dataSource se lo nomina de la siguiente manera: 
`<nombre del vendor>_<nombre del modulo>_<entity>_data_source`.
El name del dataProvider debe coincidir con el dataSource y el class apuntar a la clase dataProvider 
que creamos recientemente apuntando con la primary key de la tabla blog_post.

Lo siguiente e agregar el data source como dependencia del UI Component para que pueda cargar los datos de la tabla:
```xml
    <argument name="data" xsi:type="array">
        <item name="js_config" xsi:type="array">
            <item name="provider" xsi:type="string">devlat_blog_post_form.devlat_blog_post_data_source</item>
        </item>
        <item name="template" xsi:type="string">templates/form/collapsible</item>
    </argument>
    <settings>
        <namespace>devlat_blog_post_form</namespace>
        <dataScope>data</dataScope>
        <deps>
            <dep>devlat_blog_post_form.devlat_blog_post_data_source</dep>
        </deps>
    </settings>
```
Justo asi es como declaramos en nodo provider el data source y definimos la dependencia en settings.

Procedemos con agregar campos al form de Post:

```xml
    <fieldset name="devlat_blog_post_fieldset">
        <settings>
            <label/>
        </settings>
        <field name="id" formElement="hidden"/>
        <field name="title" formElement="input">
            <settings>
                <label translate="true">Title</label>
                <dataType>text</dataType>
            </settings>
        </field>
        <field name="content" formElement="textarea">
            <settings>
                <label translate="true">Content</label>
                <dataType>text</dataType>
            </settings>
            <formElements>
                <textarea>
                    <settings>
                        <rows>3</rows>
                    </settings>
                </textarea>
            </formElements>
        </field>
        <field name="publish_date" formElement="date">
            <settings>
                <label translate="true">Publish Date</label>
                <dataType>text</dataType>
            </settings>
        </field>
    </fieldset>
```
Agregamos el boton de Save que ejecutará la accion tanto de Add como Edit, en éste caso lo agregamos al xml de 
`devlat_blog_post_form.xml` de la siguiente manera:
En nodo settings, agregamos:
```xml
    <buttons>
        <button name="save" class="Devlat\Blog\Block\Adminhtml\Post\Edit\Button\Save"/>
    </buttons>
```

Por lo que la estructura del boton esta basado en el block que requiere el boton, y eso lo encuentra en la ruta que 
puede observar en el nodo `button` dentro de `buttons`.

Para que el boton funcione debemos darle una ruta para que lleve a un controlador `save` encargado de que haga 
el Add o Edit, por lo que agregamos dentro del nodo `dataSource` lo siguiente:
```xml
    <settings>
        <submitUrl path="*/*/save"></submitUrl>
    </settings>
```
**IMPORTANTE: ** Recuerda que el settings debes situarlo antes del dataProvider, dado que el XML mostrará un error.

En el controlador Save (`Devlat\Blog\Controller\Adminhtml\Post\Save`) está toda la lógica de edit y add del Post.

Para el agregado del boton ATRAS, es sencillo, necesitamos solo de la clase 
`Devlat\Blog\Block\Adminhtml\Post\Edit\Button\Back` y en el xml lo agregamos tal como hicimos con el botton Save:
```xml
    <button name="back" class="Devlat\Blog\Block\Adminhtml\Post\Edit\Button\Back"/>
```

Y como último detalle no olvides que las validaciones en los campos son vitales para tener datos bien 
almacenados, por ello en los fields agregamos reglas como ser:
```xml
    <validation>
        <rule name="required-entry" xsi:type="boolean">true</rule>
    </validation>
```

Puedes agregar una o más reglas para tus fields.

---

Sigue cuidadosamente la elaboración de tu grid, puede que este README te ayude y sea de mayor 
utilidad para el desarrollo y este módulo te ahorre tiempo a futuro en tu trabajo.

**Ing. Oscar Rolando Gamboa Acho.** 🇧🇴
