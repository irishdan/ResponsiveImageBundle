# Uploading

When creating a new image the responsive_image.uploader service handles uploading and saving the image file to the server.
```
$this->get('responsive_image.uploader')->upload($image);
```
for exmaple:
```
class ResponsiveImageController extends Controller
{
    ...
    ...

    /**
     * Creates a new responsiveImage entity.
     *
     * @Route("/new", name="image_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $responsiveImage = new Responsiveimage();
        $form = $this->createForm('ResponsiveImageBundle\Form\ResponsiveImageType', $responsiveImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('responsive_image.uploader')->upload($responsiveImage);

            $em = $this->getDoctrine()->getManager();
            $em->persist($responsiveImage);
            $em->flush();

            return $this->redirectToRoute('image_show', ['id' => $responsiveImage->getId()]);
        }

        return $this->render('responsiveimage/new.html.twig', [
            'responsiveImage' => $responsiveImage,
            'form' => $form->createView(),
        ]);
    }

```